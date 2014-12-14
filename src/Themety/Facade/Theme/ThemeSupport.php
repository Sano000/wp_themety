<?php

namespace Themety\Facade\Theme;

use Illuminate\Support\Facades\Facade;

class ThemeSupport extends Facade {

     public static function getFacadeAccessor()
     {
        return 'theme.theme_support';
     }

}