<?php

namespace Themety;

use Exception;

class Themety
{
    /**
     * Themety Instance
     *
     * @var \Themety\Themety
     */
    public static $inst;


    /**
     * Module instances
     *
     * @var array
     */
    protected $modules = array();

    /**
     * Module aliases
     *
     * @var array
     */
    protected $aliases = array();


    /**
     * @var array
     */
    protected $options = array();



    /**
     * Init Themety module
     */
    static function init($settings = array())
    {

        $o = array_merge(array(
            'appPath' => ABSPATH . 'wp-app/',
            'templateUri' => get_template_directory_uri(),
            ), $settings);

        $themety = self::app();
        $themety->set('core', $o);

        $files = glob($o['appPath'] . 'config/*.php');
        foreach($files as $file) {
            if (!is_file($file)) {
                continue;;
            }
            $values = include($file);
            $name = pathinfo($file, PATHINFO_FILENAME);
            $themety->set($name, $values);
        }

        $modules = $themety->get('modules');
        $modules || ($modules = array('autoload' => array()));
        foreach ($modules['autoload'] as $item) {
            is_array($item) || ($item = array($item));
            call_user_func_array([$themety, 'loadModule'], $item);
        }

        return $themety;
    }


    public static function app()
    {
        if (!self::$inst) {
            self::$inst = new self;
        }
        return self::$inst;
    }


    /**
     * Load Module
     */
    public function loadModule($class, $params = array())
    {
        if (!empty($this->modules[$class])) {
            return $this->modules[$class];
        }

        $instance = new $class($params);
        $this->modules[$class] = $instance;
        return $instance;
    }



    public static function module($class)
    {
        $themety = self::app();
        if (!empty($themety->modules[$class])) {
            return $themety->modules[$class];
        }

        $autoload = $themety->get('modules', 'autoload', array());
        foreach ($autoload as $item) {
            is_array($item) || ($item = array($item));
            if ($item[0] === $class) {
                return call_user_func_array([$themety, 'loadModule'], $item);
            }
        }

        throw new Exception("Class $class not found");
    }



    /**
     * Get value
     */
    public static function get($module, $key = null, $default = null)
    {
        $themety = self::app();
        if (!isset($themety->options[$module])) {
            return $key ? $default : array();
        }

        if (!$key) {
            return $themety->options[$module];
        }

        if (!isset($themety->options[$module][$key])) {
            return $default;
        }
        return $themety->options[$module][$key];
    }



    /**
     * Set value
     */
    public static function set($module, $key, $val = false)
    {
        $themety = self::app();
        if (!isset($themety->options[$module])) {
            $themety->options[$module] = array();
        }
        if (is_string($key)) {
            $key = array($key => $val);
        }
        $themety->options[$module] = array_merge($themety->options[$module], $key);
        return $themety;
    }


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  Utils
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
     * @param    string   $str                     String in underscore format
     * @param    bool     $capitalise_first_char   If true, capitalise the first char in $str
     * @return   string                              $str translated into camel caps
     */
    public static function toCamelCase($str, $capitalise_first_char = false) {
        if($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    /**
     * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
     * @param    string   $str    String in camel case format
     * @return    string            $str Translated into underscore format
     */
    public static function fromCamelCase($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

}
