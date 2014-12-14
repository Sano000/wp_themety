<?php

namespace Themety\Theme;

use Exception;
use Illuminate\Support\Facades\Config;

class Sidebar
{
    /**
     * Sidebar areas
     *
     * @var array
     */
    protected $sidebars = array();

    /**
     * Is new sidebars can be registered
     *
     * @var booleadn
     */
    protected $canBeRegistered = true;


    public function load()
    {
        $options = Config::get('theme.sidebars', array());
        foreach ($options as $key => $values) {
            $this->add($key, $values);
        }
    }


    /**
     * Add a sidebar
     *
     * @param string $key
     * @param mixed $values
     * @return \Themety\Theme\Sidebar
     */
    public function add($key, $values)
    {
        if (!$this->canBeRegistered) {
            throw new Exception("Too late to add a sidebar $key");
        }

        $this->sidebars[$key] = $this->prepareItem($key, $values);
        return $this;
    }


    /**
     * Register WP sidebars
     */
    public function register()
    {
        foreach ($this->sidebars as $item) {
            register_sidebar($item);
        }
        $this->canBeRegistered = false;
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
