<?php

namespace Themety\Theme;

use Exception;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

class Menu extends Base {
    use AddActions;

    /**
     * Registered menus
     *
     * @var array
     */
    protected $menus = array();

    /**
     * Is menu can be registrated
     *
     * @var boolean
     */
    protected $canRegister = true;

    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'menus', array());
        foreach ($options as $key => $value) {
            $this->register($key, $value);
        }
    }


    /**
     * Add menu
     *
     * @param string $name
     * @param string $description
     * @return \Themety\Theme\Menu
     */
    public function register($name, $description = '')
    {
        if (!$this->canRegister) {
            throw new Exception("Too late to register menu: $name");
        }

        $this->menus[$name] = $description;
        return $this;
    }


    /**-----------------------------------------------------------------------------------------------------------------
     *
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Register WP menus
     */
    public function onInit()
    {
        $this->menus && register_nav_menus($this->menus);
        $this->canRegister = false;
    }

}
