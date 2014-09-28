<?php

namespace Themety\Metabox\Field;

use Themety\Traits\View;

abstract class Base
{
    use View;

    protected $templatesPath = 'templates';
    protected $templateName = '';
    protected $defaults = array();

    public function __construct()
    {

    }

    /**
     * Get Meta Values
     */
    public function get($postId, $meta_name)
    {
        $value = get_post_meta($postId, $meta_name, true);
        return $value;
    }

    /**
     * Render metabox
     */
    public function render($data, $values)
    {
        $data = $this->prepareData($data);

        $content = '';
        $content .= $this->renderBeforeInput($data);
        $content .= $data['multi'] ? $this->renderMultiField($data, $values) : $this->renderSingleField($data, $values);
        $content .= $this->renderAfterInput($data);
        return $content;
    }

    /**
     * Render single field
     *
     * @param array $data
     * @param mixed $value
     * @return string
     */
    protected function renderSingleField($data, $value)
    {
        $attributes = $this->getAttributes($data);
        $template = $this->templateName ? : strtolower(array_pop(explode('\\', get_class($this))));

        $content = $this->view($template, compact('data', 'value', 'attributes') + $this->templateParams($data));
        return $content;
    }

    /**
     * Render several fields
     *
     * @param array $data
     * @param mixed $values
     * @return string
     */
    protected function renderMultiField($data, $values)
    {
        $content = '';
        $items = empty($data['multi']['items']) ? count($values) : max(count($values), $data['multi']['items']);
        for ($n = 0; $n < $items; $n++) {
            $content .= '<div class="input-item">';
            $content .= $this->renderSingleField($data, $values[$n]);
            $content .= '</div>';
        }
        return $content;
    }

    /**
     * Render field header (and metadata)
     *
     * @param array $data
     * @return string
     */
    protected function renderBeforeInput($data)
    {
        $content = '<input type="hidden" name="themety_meta_fields[]" '
            . 'value="' . base64_encode(serialize(array('id' => $data['id'], 'class' => get_class($this)))) . '">';
        if ($data['description']) {
            $content .= '<label>' . $data['description'] . '</label>';
        }
        return $content;
    }

    /**
     * Render field footer
     *
     * @param array $data
     * @return string
     */
    protected function renderAfterInput($data)
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
     * @param array $data
     * @return array
     */
    protected function templateParams($data)
    {
        return array();
    }

    /**
     * Render input attributes
     *
     * @param array $data
     * @return string
     */
    protected function getAttributes($data)
    {
        $result = array();
        $attributes = $data['attributes'];
        foreach ($attributes as $key => $value) {
            $result[] = $key . '=' . $value;
        }
        return implode(' ', $result);
    }
}
