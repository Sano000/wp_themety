<?php

namespace Themety;

use Exception;

use Themety\Traits\AddActions;

use Themety\Themety;
use Themety\Tools\Options;

class Routes extends Base
{
    use AddActions;

    /**
     * Rewrite rules
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Is a new rule can be registrated
     *
     * @var boolean
     */
    protected $ruleCanBeRegistrated = true;


    public function __construct()
    {
        $this->bindAddActions();

        $rules = Themety::get('routes', 'rules', array());
        foreach ($rules as $key => $item) {
            $this->register($key, $item);
        }
    }



    /**
     * Register a new rewrite rule
     *
     * @param string $key
     * @param array $data
     * @return \Themety\Routes
     */
    public function register($key, array $data)
    {
        if (!$this->ruleCanBeRegistrated) {
            throw new Exception("Too late to register a new rewrite rule");
        }

        $this->rules[$key] = $this->parseRule($key, $data);
        return $this;
    }



    protected function parseRule($key, $rule)
    {
        is_string($rule) && ($rule = array('rule' => $rule));
        if (empty($rule['rule'])) {
            throw new Exception("Route rule cannot be empty");
        }

        if (empty($rule['redirect'])) {
            $count = substr_count($rule['rule'], '(');
            $vars = array();
            for ($n=1; $n<=$count; $n++) {
                $vars[] = 'themety_var[]=$matches[' . $n . ']';
            }

            $rule['redirect'] = "index.php?themety_action=$key" . ($count ? '&' . join('&', $vars) : '');
        }

        $result = array_merge(array(
            'rule' => null,
            'callback' => null,
            'redirect' => null,
            'after' => 'bottom'
        ), $rule);

        if (!in_array($result['after'], array('top', 'bottom'))) {
            throw new Exception("'top' or 'bottom' values only allowed for 'after' key");
        }

        if (!empty($result['callback']) && !is_callable($result['callback'])) {
            throw new Exception("Callback should be a valid callable function");
        }

        return $result;
    }


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/

    public function onInit()
    {
        $str = '';
        foreach ($this->rules as $item) {
            $str .= $item['rule'] . $item['after'] . $item['redirect'];
            add_rewrite_rule($item['rule'], $item['redirect'], $item['after']);
        }
        $hash = md5($str);
        if ($hash !== Options::getThemety('rewrite_hash')) {
            global $wp_rewrite;
            $wp_rewrite->flush_rules(true);
            Options::setThemety('rewrite_hash', $hash);
        }

        $this->ruleCanBeRegistrated = false;
    }


    public function onParseRequest($wp)
    {
        $action = isset($wp->query_vars['themety_action']) ? $wp->query_vars['themety_action'] : null;

        if ($action && isset($this->rules[$action])) {
            $rule = $this->rules[$action];
            if (is_callable($rule['callback'])) {
                call_user_func_array($rule['callback'], $wp->query_vars['themety_var']);
            }
        }
    }


    public function onQueryVars($vars)
    {
        $vars[] = 'themety_action';
        $vars[] = 'themety_var';
        return $vars;
    }
}