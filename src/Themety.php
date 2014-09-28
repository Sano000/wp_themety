<?php

use Themety\Config;

class Themety
{

    static $inst;
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
        $themety->set($o);

        $themety->set('config', new Config);

        $themety->config->load('modules');
        $modules = $themety->config->get('modules', array());

        foreach($modules as $key => $data) {
            $themety->loadModule($key, $data);
        }

        return $themety;
    }


    /**
     * Load module
     *
     * @param type $key
     * @param type $item
     * @return boolean
     */
    public function loadModule($key, $item) {
        if (!is_array($item)) {
            $item = array('class' => $item);
        }
        if (!isset($item['class'])) {
            return false;
        }
        $item = array_merge(array(
            'class' => '',
            'key' => $key,
            'config' => $key,
            'init' => 'init'
        ), $item);

        $this->config->load($item['config']);
        $obj = new $item['class']($this->config->get($item['config']));
        $this->set($item['key'], $obj);

        method_exists($obj, $item['init']) && $obj->init();
        return $this;
    }



    public static function app()
    {
        if (!self::$inst) {
            self::$inst = new self;
        }
        return self::$inst;
    }


    public static function get($key)
    {
        return self::app()->{$key};
    }


    public function set($key, $val = false)
    {
        if (is_string($key)) {
            $key = array($key => $val);
        }
        $this->options = array_merge($this->options, $key);
        return $this;
    }



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





    public function __get($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : false;
    }

    protected function __clone()
    {
        return false;
    }

    public function __call($name, $arguments)
    {
        return false;
    }

    public static function __callStatic($name, $arguments)
    {
        $obj = self::get($name);
        if (!$obj) {
            throw new Exception( 'Themety object not found: ' . $name);
        }
        return $obj;
    }

}
