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
        foreach ($methods as $method) {
            if (preg_match('/^sc(\S+)$/', $method, $matches)) {
                $name = Themety::fromCamelCase($matches[1]);
                add_shortcode($name, array($this, $method));
            }
        }
    }
}
