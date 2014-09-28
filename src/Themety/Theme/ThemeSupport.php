<?php

namespace Themety\Theme;

use Themety\Base;
use Themety\Themety;

class ThemeSupport extends Base {

    public function __construct()
    {
        $options = Themety::get('theme_support');
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                add_theme_support($value);
            } else {
                add_theme_support($key, $value);
            }
        }
    }
}
