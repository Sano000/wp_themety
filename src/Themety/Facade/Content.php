<?php

namespace Themety\Facade;

use Illuminate\Support\Facades\Facade;

class Content extends Facade {

    public static function getFacadeAccessor()
    {
       return 'content';
    }

}