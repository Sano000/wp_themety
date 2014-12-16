<?php

namespace Themety\Facade\Theme;

use Illuminate\Support\Facades\Facade;

class Scripts extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'theme.scripts';
    }
}
