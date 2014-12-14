<?php

namespace Themety\Facade\Theme;

use Illuminate\Support\Facades\Facade;

class ImageSizes extends Facade {

     public static function getFacadeAccessor()
     {
        return 'theme.image_sizes';
     }

}