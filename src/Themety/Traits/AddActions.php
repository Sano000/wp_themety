<?php

namespace Themety\Traits;

use Themety\Themety;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response;

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
                add_action($action, [$this, $method], $priority, $arguments);
                $count ++;
            }

            if (preg_match('/^ajax(Nopriv)?(\S+)$/', $method, $matches)) {
                $key = Themety::fromCamelCase($matches[2]);
                $action = "wp_ajax" . ($matches[1] ? '_nopriv' : '') . "_$key";
                add_action($action, [$this, 'callAjaxAction']);
            }
        }

        return $count;
    }

    /**
     * Call ajax action
     */
    public function callAjaxAction()
    {
        $key = Themety::toCamelCase(Input::get('action'), true);
        $method = is_user_logged_in() ? "ajax$key" : "ajaxNopriv$key";

        $response = call_user_func([$this, $method]);

        if ($response instanceof Response) {
            $response->send();
        } else {
            echo $response;
        }
        die();
    }
}
