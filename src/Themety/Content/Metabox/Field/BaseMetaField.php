<?php

namespace Themety\Content\Metabox\Field;

use Exception;

use SplObjectStorage;
use Themety\Traits\View;
use Themety\Facade\Themety;

class BaseMetaField extends SplObjectStorage
{
    use View;

    protected $templatesPath = 'templates';
    protected $templateName = '';
    protected $defaults = array();
    protected $itemClass = 'Themety\Content\Metabox\SingleField';

    /**
     * Field value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Base post
     *
     * @var \Themety\Model\Tools\PostModel
     */
    protected $post;

    /**
     * Field data
     */
    protected $fieldData;


    /**
     * Constructor
     *
     * @param \Themety\Model\Tools\PostModel $post
     * @param array $fieldData
     */
    public function __construct(\Themety\Model\Tools\PostModel $post = null, array $fieldData = array())
    {
        $this->fieldData = $fieldData;

        if ($post) {
            $this->setPost($post);
            $this->setFieldData($fieldData);
            $this->fill();
        }
    }

    public function __get($name)
    {
        $this->valid() || $this->rewind();

        if ($name === 'value') {
            return $this->current()->getValue();
        }

        return $this->current()->{$name};
    }


    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->current(), $name], $arguments);
    }

    /**
     * Fill collection
     */
    public function fill()
    {
        $value = get_post_meta($this->post->ID, $this->fieldData['id'], true);
        is_array($value) || ($value = array($value));

        foreach ($value as $v) {
            $this->setValue($v);
        }
        $this->rewind();

        return $this;
    }

    /**
     * Add single value to collection
     *
     * @param mixed $value
     * @return \Themety\Metabox\Field\Base
     */
    public function setValue($value)
    {
        $item = new $this->itemClass($value);
        $this->attach($item);
        return $this;
    }


    /**
     * Convert value to string
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->count()) {
            return '';
        }

        $value = $this->current()->getValue();
        if (!$value) {
            return '';
        }
        return is_array($value) || is_object($value) ? json_encode($value) : $value;
    }

    /**
     * To array convertation
     *
     * @return type
     */
    public function toArray()
    {
        if (!$this->fieldData['multi']) {
            return $this->current()->getValue();
        }

        $result = [];
        foreach ($this as $item) {
            $result[] = $item->getValue();
        }

        return $result;
    }


    /**
     * Set PostModel
     *
     * @param \Themety\Model\Tools\PostModel $post
     * @return \Themety\Content\Metabox\Field\BaseMetaField
     */
    public function setPost(\Themety\Model\Tools\PostModel $post)
    {
        $this->post = $post;
        return $this;
    }

    /**
     * Set field data
     *
     * @param array $fieldData
     * @return \Themety\Metabox\Field\Base
     */
    public function setFieldData(array $fieldData)
    {
        if (empty($fieldData['id'])) {
            throw new Exception("Check meta field configuration");
        }

        $this->fieldData = $fieldData;
        $this->fieldData = array_replace_recursive($this->getDefaults(), $this->defaults, $this->fieldData);

        if (!is_array($this->fieldData['post_type'])) {
            $this->fieldData['post_type'] = array($this->fieldData['post_type']);
        }

        if (isset($this->fieldData['post_id']) && !is_array($this->fieldData['post_id'])) {
            $this->fieldData['post_id'] = array($this->fieldData['post_id']);
        }

        if (empty($this->fieldData['callback_args'])) {
            $this->fieldData['callback_args'] = $this->fieldData;
        }

        return $this;
    }


    /**
     * Get Field data
     *
     * @return array
     */
    public function getFieldData()
    {
        return $this->fieldData;
    }

    /**
     * Store field data into database
     *
     * @param mixed $value
     */
    public function save($value)
    {
        if (is_array($value)) {
            $value = array_values(array_filter($value));
        }
        if ($value !== $this->value) {
            update_post_meta($this->post->ID, $this->fieldData['id'], $value);
        }
    }


    /**
     * Render metabox
     *
     * @return string Html string
     */
    public function render()
    {
        $content = '';
        $content .= $this->renderBeforeInput();
        $content .= $this->fieldData['multi'] ? $this->renderMultiField() : $this->renderSingleField();
        $content .= $this->renderAfterInput();

        return $content;
    }


    /**
     * Render and show field
     */
    public function show()
    {
        echo $this->render();
    }

    /**
     * Render single field
     *
     * @return string Html string
     */
    protected function renderSingleField()
    {
        $content = $this->view($this->getTemplate(), array_merge([
            'data' => $this->fieldData,
            'value' => $this->current(),
            'attributes' => $this->getAttributes(),
        ], $this->templateParams()));

        return $content;
    }


    /**
     * Render several fields
     *
     * @return string
     */
    protected function renderMultiField()
    {
        $content = '';
        $values = $this->value;
        $items = empty($this->fieldData['multi']['items'])
            ? count($values) : max(count($values), $this->fieldData['multi']['items']);

        $this->rewind();
        for ($n = 0; $n < $items; $n++) {
            $content .= '<div class="input-item">';
            $content .= $this->renderSingleField();
            $content .= '</div>';
            $this->next();
        }

        $this->value = $values;
        return $content;
    }

    /**
     * Render field header (and metadata)
     *
     * @return string
     */
    protected function renderBeforeInput()
    {
        $content = '<input type="hidden" name="themety_meta_fields[]" '
            . 'value="' . base64_encode(serialize(
                array(
                'id' => $this->fieldData['id'], 'class' => get_class($this))
            )) . '">';
        if ($this->fieldData['description']) {
            $content .= '<label>' . $this->fieldData['description'] . '</label>';
        }
        return $content;
    }


    /**
     * Render field footer
     *
     * @return string
     */
    protected function renderAfterInput()
    {
        return '';
    }


    /**
     * Get template name
     *
     * @return string
     */
    protected function getTemplate()
    {
        $class = explode('\\', get_class($this));
        $name = array_pop($class);
        $template = $this->templateName ? : Themety::fromCamelCase($name);
        return $template;
    }


    /**
     * Send additional parameters to template
     *
     * @return array
     */
    protected function templateParams()
    {
        return array();
    }

    /**
     * Render input attributes
     *
     * @return string
     */
    protected function getAttributes()
    {
        $result = array();
        $this->fieldData['attributes']['name'] = $this->getNameAttribute();
        $attributes = $this->fieldData['attributes'];

        foreach ($attributes as $key => $value) {
            $result[] = $key . '="' . $value . '"';
        }
        return implode(' ', $result);
    }


    public function getData($key)
    {
        return isset($this->fieldData[$key]) ? $this->fieldData[$key] : null;
    }

    public function setData($key, $value)
    {
        $this->fieldData[$key] = $value;
        return $this;
    }


    public function getNameAttribute()
    {
        return empty($this->fieldData['attributes']['name']) ?
            ($this->fieldData['id'] . ($this->fieldData['multi'] ? '[]' : '')) :
            $this->fieldData['attributes']['name'];
    }


    public function setNameAttribute($value)
    {
        $this->fieldData['attributes']['name'] = $value;
        return $this;
    }

    /**
     * Get defaults
     *
     * @param type $id
     * @param array $data
     * @return type
     */
    public function getDefaults()
    {
        return array(
            'id' => null,
            'title' => '',
            'callback' => array($this, 'show'),
            'callback_args' => null,
            'context' => 'advanced',
            'priority' => 'default',
            'post_type' => false,
            'post_id' => false,
            'description' => '',
            'field_type' => 'text',
            'attributes' => array(),
            'multi' => false,
        );
    }
}
