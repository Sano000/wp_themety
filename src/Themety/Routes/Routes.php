<?php

namespace Themety\Routes;

use Exception;
use Themety\Tools\Options;
use Illuminate\Support\Facades\Config;

class Routes
{
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


    public function load()
    {
        $rules = Config::get('routes.rules', array());
        foreach ($rules as $key => $item) {
            $this->add($key, $item);
        }
    }



    /**
     * Add a new rewrite rule
     *
     * @param string $key
     * @param array $data
     * @return \Themety\Routes
     */
    public function add($key, array $data)
    {
        if (!$this->ruleCanBeRegistrated) {
            throw new Exception("Too late to add a new rewrite rule");
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


    public function register()
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


    public function parseQuery($wp)
    {
        $action = isset($wp->query_vars['themety_action']) ? $wp->query_vars['themety_action'] : null;

        if ($action && isset($this->rules[$action])) {
            $rule = $this->rules[$action];
            if (is_callable($rule['callback'])) {
                $arguments = is_array($wp->query_vars['themety_var']) ? $wp->query_vars['themety_var'] : array();
                call_user_func_array($rule['callback'], $arguments);
            }
        }
    }


    public function updateQueryVars($vars)
    {
        $vars[] = 'themety_action';
        $vars[] = 'themety_var';
        return $vars;
    }
}
