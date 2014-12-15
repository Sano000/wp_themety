<?php

namespace Themety\Facade;

use Illuminate\Support\Facades\Facade;

class Routes extends Facade
{

    public static function getFacadeAccessor()
    {
        return 'routes';
    }
}
