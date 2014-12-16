<?php

namespace Themety\Facade;

use Illuminate\Support\Facades\Facade;

class Widgets extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'widgets';
    }
}
