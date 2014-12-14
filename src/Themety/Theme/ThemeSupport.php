<?php

namespace Themety\Theme;

use Exception;
use Illuminate\Support\Facades\Config;

class ThemeSupport
{

    protected $features = array();
    protected $afterSetupThemeCalled = false;

    public function load()
    {
        $options = Config::get('theme.theme_support', array());
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $this->register($value);
            } else {
                $this->register($key, $value);
            }
        }
    }


    /**
     * Add theme support item
     *
     * @param string $feature
     * @param array $arguments
     */
    public function add($feature, $arguments = array()) {
        if ($this->afterSetupThemeCalled) {
            throw new Exception("Theme support should be set within the 'after_setup_theme' event");
        }

        if (array_key_exists($feature, $this->features)) {
            return $this;
        }

        add_theme_support($feature, $arguments);
        $this->features[$feature] = $arguments;
        return $this;
    }


    /**
     *
     */
    public function register()
    {
        $this->afterSetupThemeCalled = true;
    }

}
