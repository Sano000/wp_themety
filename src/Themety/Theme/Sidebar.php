<?php

namespace Themety\Theme;

use Exception;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

class Sidebar extends Base {
    use AddActions;

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


    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'sidebars', array());
        foreach ($options as $key => $values) {
            $this->register($key, $values);
        }
    }


    /**
     * Register sidebar
     *
     * @param string $key
     * @param mixed $values
     * @return \Themety\Theme\Sidebar
     */
    public function register($key, $values)
    {
        if (!$this->canBeRegistered) {
            throw new Exception("Too late to register sidebar $key");
        }

        $this->sidebars[$key] = $this->prepareItem($key, $values);
        return $this;
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


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                              ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/

    /**
     * Register WP sidebars
     */
    public function onWidgetsInit()
    {
        foreach ($this->sidebars as $item) {
            register_sidebar($item);
        }
        $this->canBeRegistered = false;
    }

}
