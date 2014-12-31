<?php

namespace Themety\Traits;

use Themety\Themety;

trait AddActions
{

    /**
     * Bind all "on" started function to actions
     */
    public function bindAddActions()
    {
        $methods = get_class_methods($this);

        $count = 0;
        foreach ($methods as $method) {
            if (preg_match('/^on(\S+)(P(\d+))?(A(\d+))?$/U', $method, $matches)) {
                $action = Themety::fromCamelCase($matches[1]);
                $priority = empty($matches[3]) ? 10 : $matches[3];
                $arguments = empty($matches[5]) ? 1 : $matches[5];
                add_action($action, array($this, $method), $priority, $arguments);
                $count ++;
            }
        }

        return $count;
    }
}
