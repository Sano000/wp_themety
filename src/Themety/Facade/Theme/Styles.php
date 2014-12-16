<?php

namespace Themety\Facade\Theme;

use Illuminate\Support\Facades\Facade;

class Styles extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'theme.styles';
    }
}
