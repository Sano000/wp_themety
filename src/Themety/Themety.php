<?php

namespace Themety;

class Themety
{
    /**
     * Themety Instance
     *
     * @var \Themety\Themety
     */
    public static $inst;


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
            'basePath' => ABSPATH,
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
        $modules || ($modules = array());
        foreach ($modules as $class => $params) {
            if (is_numeric($class)) {
               $class = $params;
               $params = null;
            }
            new $class($params);
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
