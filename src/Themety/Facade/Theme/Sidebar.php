<?php

namespace Themety\Facade\Theme;

use Illuminate\Support\Facades\Facade;

class Sidebar extends Facade {

     public static function getFacadeAccessor()
     {
        return 'theme.sidebar';
     }

}