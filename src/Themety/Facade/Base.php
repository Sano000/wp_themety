<?php

namespace Themety\Facade;

use Exception;

use Themety\Themety;

abstract class Base {

    abstract static function getFacadeAccessor();

    public static function __callStatic($method, $arguments)
    {
        $className = get_called_class();
        $facaded = $className::getFacadeAccessor();
        $facadedInstance = Themety::module($facaded);

        if (!method_exists($facaded, $method)) {
            throw new Exception("Method not exitst: $facaded::$method");
        }

        return call_user_func_array([$facadedInstance, $method], $arguments);
    }

}
