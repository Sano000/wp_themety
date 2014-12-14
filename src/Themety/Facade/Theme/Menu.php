<?php

namespace Themety\Facade\Theme;

use Illuminate\Support\Facades\Facade;

class Menu extends Facade {

     public static function getFacadeAccessor()
     {
        return 'theme.menu';
     }

}