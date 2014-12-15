<?php

namespace Themety;

use ReflectionClass;

abstract class Base
{


    public static function getAssetUri($file, $absolute = true)
    {
        $reflector = new ReflectionClass(get_called_class());
        $fn = pathinfo($reflector->getFileName(), PATHINFO_DIRNAME);
        $fn = preg_replace('/' . preg_quote(__NAMESPACE__) . '$/', '', $fn);
        $fn = realpath($fn . '..');
        $fn = preg_replace('/^' . preg_quote(ABSPATH, '/') . '/', '', $fn);
        $fn = "/$fn/assets/$file";
        $absolute && ($fn = get_site_url() . $fn);
        
        return $fn;
    }
}
