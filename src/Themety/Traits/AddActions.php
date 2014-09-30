<?php

namespace Themety\Traits;

use Themety\Themety;


trait AddActions {

    /**
     * Bind all "on" started function to actions
     */
    public function bindAddActions()
    {
        $methods = get_class_methods($this);

        $count = 0;
        foreach ($methods as $method) {
            if (preg_match('/^on(\S+)/', $method, $matches)) {
                $action = Themety::fromCamelCase($matches[1]);
                add_action($action, array($this, $method));
                $count ++;
            }
        }

        return $count;
    }
}
