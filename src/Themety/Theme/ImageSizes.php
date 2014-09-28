<?php

namespace Themety\Theme;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

class ImageSizes extends Base {
    use AddActions;

    public function init()
    {
        $this->bindAddActions();
    }

    /**
     * Add image sizes
     */
    public function onAfterSetupTheme()
    {
        $options = Themety::get('image_sizes');
        foreach ($options as $key => $size) {
            if ($size) {
                array_unshift($size, $key);
                call_user_func_array('add_image_size', $size);
            }
        }
    }
}