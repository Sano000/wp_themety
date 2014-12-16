<?php

namespace Themety\Widget;

use WP_Widget;
use Themety\Traits\View;

abstract class BaseWidget extends WP_Widget
{
    use View;

    protected $widgetId;
    protected $widgetName = '';
    protected $widgetOptions = array();
    protected $widgetControlOptions = array();
    protected $includeTitleField = true;
    protected $fields = array();
    protected $templatesPath = 'templates';

    public function __construct()
    {
        parent::__construct($this->widgetId, $this->widgetName, $this->widgetOptions, $this->widgetControlOptions);

        if ($this->includeTitleField) {
            $title = array(
                'title' => array(
                    'type' => 'text',
                    'default' => __('Title'),
                    'label' => __('Title'),
                    'filter' => 'strip_tags',
                ));
            $this->fields = array_merge($title, $this->fields);

            foreach ($this->fields as $key => $item) {
                if (!is_array($item)) {
                    unset($this->fields[$key]);
                }
            }
        }

        foreach ($this->fields as $key => &$field) {
            $field = $this->getFieldDefaults($field);
            $field['id'] = $key;
            $class = 'Themety\Widget\Field\\' . ucfirst($field['type']);
            $field['class'] = new $class($field);
        }
    }

    public function form($instance)
    {
        $content = '';
        foreach ($this->fields as $key => $field) {
            $data = array(
                'default' => array_key_exists($key, $instance) ? $instance[$key] : $field['default'],
                'id' => $this->get_field_id($key),
                'name' => $this->get_field_name($key) . ( $field['multi'] ? '[]' : ''),
            );
            $content .= $field['class']->render($data, $instance);
        }
        echo $content;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        foreach ($this->fields as $key => $field) {
            $instance[$key] = $field['class']->prepareToSave($new_instance, $old_instance);
        }

        return $instance;
    }

    public function widget($widget, $instance)
    {
        echo $this->view($this->widgetId, array(
            'instance' => $instance,
        ));
    }

    /* -------------------------------------------------------------------------- */

    protected function getFieldDefaults($fieldData)
    {
        return array_merge(array(
            'type' => 'text',
            'default' => null,
            'label' => _('Text Field'),
            'filter' => null,
            'multi' => false,
            ), $fieldData);
    }

    protected function renderBeforeContent($widget = array(), $instance = array())
    {
        $widget = $widget ? : $this->currentWidgetSettings;
        $instance = $instance ? : $this->currentInstance;

        $content = $widget['before_widget'];
        if (!empty($instance['title'])) {
            $content .= $widget['before_title']
                . apply_filters('widget_title', $instance['title']) . $widget['after_title'];
        }
        return $content;
    }

    protected function renderAfterContent($widget = array(), $instance = array())
    {
        $widget = $widget ? : $this->currentWidgetSettings;
        $instance = $instance ? : $this->currentInstance;
        $content = $widget['after_widget'];
        return $content;
    }
}
