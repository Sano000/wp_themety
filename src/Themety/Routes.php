<?php

namespace Themety;

use Exception;

use Themety\Traits\AddActions;

use Themety\Themety;

class Routes extends Base
{
    use AddActions;

    /**
     * Rewrite rules
     *
     * @var array
     */
    protected $rules = array();


    public function __construct()
    {
        $this->bindAddActions();
    }



    public function onInit()
    {
        $rules = Themety::get('routes', 'rules', array());
        foreach ($rules as $key => $item) {
            $this->parseRule($key, $item);

        }
    }


    public function onQueryVars($vars)
    {
        $vars[] = 'themety_action';
        $vars[] = 'themety_var';
        return $vars;
    }



    protected function parseRule($key, $rule)
    {
        is_string($rule) && ($rule = array('rule' => $rule));
        $result = array_merge(array(
            'rule' => null,
            'callback' => null,
            'after' => 'bottom'
        ), $rule);

        if (empty($result['rule'])) {
            throw new Exception("Route rule cannot be empty");
        }

        if (!in_array($result['after'], array('top', 'bottom'))) {
            throw new Exception("'top' or 'bottom' values only allowed for 'after' key");
        }

        if (!is_callable($result['callback'])) {
            throw new Exception("Callback should be a valid callable function");
        }

        return $result;
    }

}