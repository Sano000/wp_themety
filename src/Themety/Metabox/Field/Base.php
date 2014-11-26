<?php

namespace Themety\Metabox\Field;

use Exception;

use Themety\Themety;
use SplObjectStorage;
use Themety\Traits\View;

class Base extends SplObjectStorage
{
    use View;

    protected $templatesPath = 'templates';
    protected $templateName = '';
    protected $defaults = array();
    protected $itemClass = 'Themety\Metabox\SingleField';

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
        if ($post) {
            $this->setFieldData($post, $fieldData);
            $this->fill();
        }
    }

    public function __get($name)
    {
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

        foreach($value as $v) {
            $item = new $this->itemClass($v);
            $this->attach($item);
        }
        $this->rewind();

        return $this;
    }


    /**
     * Convert value to string
     *
     * @return string
     */
    public function __toString()
    {
        $value = $this->get();
        return is_array($value) || is_object($value) ? json_encode($value) : $value;
    }

    /**
     * To array convertation
     *
     * @return type
     */
    public function toArray()
    {
        if(!$this->fieldData['multi']) {
            return $this->current()->getValue();
        }

        $result = [];
        foreach ($this as $item) {
            $result[] = $item->getValue();
        }

        return $result;
    }


    /**
     * Set field data - PostModel and field data array
     *
     * @param \Themety\Model\Tools\PostModel $post
     * @param array $fieldData
     * @return \Themety\Metabox\Field\Base
     */
    public function setFieldData(\Themety\Model\Tools\PostModel $post, array $fieldData)
    {
        if (empty($fieldData['id'])) {
            throw new Exception("Check meta field configuration");
        }
        $this->post = $post;
        $this->fieldData = $this->prepareData($fieldData);
        return $this;
    }


    /**
     * Store field data into database
     *
     * @param mixed $value
     */
    public function save($value)
    {
//        $old = get_post_meta($postId, $key, is_array($new) ? false : true);
//        $this->value
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
     * Render single field
     *
     * @return string Html string
     */
    protected function renderSingleField()
    {
        $attributes = $this->getAttributes();
        $template = $this->templateName ? : Themety::fromCamelCase(array_pop(explode('\\', get_class($this))));

        $content = $this->view($template, array_merge([
            'data' => $this->fieldData,
            'value' => $this->current(),
            'attributes' => $attributes
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
            . 'value="' . base64_encode(serialize(array('id' => $this->fieldData['id'], 'class' => get_class($this)))) . '">';
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
     * Merge data with default values
     */
    protected function prepareData($data)
    {
        $attributes = empty($this->defaults['attributes']) ? array() : $this->defaults['attributes'];
        $data = array_merge($this->defaults, $data);
        foreach ($attributes as $key => $value) {
            if (!array_key_exists($key, $data['attributes'])) {
                $data['attributes'][$key] = $value;
            }
        }
        $data['attributes']['name'] = $data['id'] . ( $data['multi'] ? '[]' : '' );
        return $data;
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
        $attributes = $this->fieldData['attributes'];
        foreach ($attributes as $key => $value) {
            $result[] = $key . '=' . $value;
        }
        return implode(' ', $result);
    }
}
