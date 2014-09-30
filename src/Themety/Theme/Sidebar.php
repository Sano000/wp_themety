<?php

namespace Themety\Theme;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

class Sidebar extends Base {
    use AddActions;

    public function init()
    {
        $this->bindAddActions();
    }

    /**
     * Register WP sidebars
     */
    public function onWidgetsInit()
    {
        $options = Themety::get('theme', 'sidebars', array());
        foreach ($options as $key => $values) {
            $data = $this->prepareItem($key, $values);
            register_sidebar($data);
        }
    }



    protected function prepareItem($key, $values) {
        if (!is_array($values)) {
            $values = array('name' => $values);
        }
        $values = array_merge(array(
            'id' => $key,
            'name' => __('Sidebar'),
            'description' => '',
            'class' => '',
            'before_widget' => '<div>',
            'after_widget' => '</div>',
            'before_title' => '<h2>',
            'after_title' => '</h2>',
            'group' => '',
            ), $values);

        return $values;
    }

}
