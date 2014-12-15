<?php

namespace Themety\Theme;

use Exception;
use Illuminate\Support\Facades\Config;

class Menu
{
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

    public function load()
    {
        $options = Config::get('theme.menus', array());
        foreach ($options as $key => $value) {
            $this->add($key, $value);
        }
    }


    /**
     * Add menu
     *
     * @param string $name
     * @param string $description
     * @return \Themety\Theme\Menu
     */
    public function add($name, $description = '')
    {
        if (!$this->canRegister) {
            throw new Exception("Too late to add menu: $name");
        }

        $this->menus[$name] = $description;
        return $this;
    }


    /**
     * Register WP menus
     */
    public function register()
    {
        $this->menus && register_nav_menus($this->menus);
        $this->canRegister = false;
    }
}
