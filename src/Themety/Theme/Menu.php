<?php

namespace Themety\Theme;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

class Menu extends Base {
    use AddActions;

    public function __construct()
    {
        $this->bindAddActions();
    }

    /**
     * Register WP menus
     */
    public function onInit()
    {
        $options = Themety::get('menu');
        $options && register_nav_menus($options);
    }

}
