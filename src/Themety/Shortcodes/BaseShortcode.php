<?php

namespace Themety\Shortcodes;

use Themety\Traits\View;
use Themety\Facade\Themety;

abstract class BaseShortcode
{
    use View;


    public function __construct()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $methods) {
            if (strpos($methods, 'sc') !== false) {
                $name = str_replace('sc', '', $methods);
                $name = Themety::fromCamelCase($name);

                add_shortcode($name, array($this, $methods));
            }
        }
    }
}
