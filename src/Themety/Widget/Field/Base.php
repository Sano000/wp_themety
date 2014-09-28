<?php

namespace Themety\Widget\Field;

use Themety\Traits\View;

abstract class Base
{
    use View;

    protected $templatesPath = 'templates';
    protected $templateName = '';
    protected $defaults = array();
    protected $data = array();

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function render($data, $instance)
    {
        $data['field'] = $this->getDefaults($this->data);
        $data['instance'] = $instance;
        $values = $this->getValues($data['field']['id'], $instance, $data['field']['default']);
        $data = $this->prepareData($data);

        $content = $this->renderBeforeInput($data);
        $content .= $data['field']['multi']
            ? $this->renderMultiField($data, $values) : $this->renderSingleField($data, $values);
        $content .= $this->renderAfterInput($data);

        return $content;
    }

    /**
     * Prepare data before field render
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        return $data;
    }

    /**
     * Get Values
     *
     * @param array $instanceValues
     * @return mixed
     */
    protected function getValues($id, $instanceValues, $default = '')
    {
        return empty($instanceValues[$id]) ? $default : $instanceValues[$id];
    }

    /**
     * Get input field attributes
     * @param integer $n
     */
    protected function getAttributes($data)
    {
        $result = array();
        $attributes = array(
            'id' => $data['id'],
            'name' => $data['name'],
        );
        $attributes = empty($data['field']['attributes'])
            ? $attributes : array_merge($data['field']['attributes'], $attributes);
        foreach ($attributes as $key => $value) {
            $result[] = $key . '=' . $value;
        }
        return implode(' ', $result);
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
        $template = $this->templateName ? : $data['field']['type'];
        $content = $this->view($template, compact('data', 'value', 'attributes'));
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
        $items = max($data['field']['multi']['items'], count($values));
        for ($n = 0; $n < $items; $n ++) {
            $value = empty($values[$n]) ? $data['field']['default'] : $values[$n];
            $content .= '<div class="input-item">';
            $content .= $this->renderSingleField($data, $value);
            $content .= '</div>';
        }
        return $content;
    }

    protected function renderBeforeInput($data)
    {
        $content = '<div><label for="' . $data['id'] . '">' . $data['field']['label'] . ':</label>';
        return $content;
    }

    protected function renderAfterInput($data)
    {
        return '</div>';
    }

    public function prepareToSave($newInstance, $oldInstance)
    {
        $value = false;
        if (array_key_exists($this->data['id'], $newInstance)) {
            $value = $newInstance[$this->data['id']];
            if ($this->data['filter']) {
                if (is_array($value)) {
                    $value = array_values(array_filter($value));
                    foreach ($value as &$item) {
                        $item = call_user_func($this->data['filter'], $item);
                    }
                } else {
                    $value = call_user_func($this->data['filter'], $value);
                }
            }
        }
        return $value;
    }

    public function getDefaults($data)
    {
        $result = array_merge(array(
            'type' => '',
            'label' => '',
            'default' => '',
            'multi' => '',
            ), $this->defaults, $data);

        return $result;
    }
}
