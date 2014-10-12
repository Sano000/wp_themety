<?php

namespace Themety\Theme;

use Exception;

use Themety\Traits\AddActions;

use Themety\Base;
use Themety\Themety;

class ThemeSupport extends Base {
    use AddActions;

    protected $features = array();
    protected $afterSetupThemeCalled = false;

    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'theme_support', array());
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $this->register($value);
            } else {
                $this->register($key, $value);
            }
        }
    }


    /**
     * Add theme support
     *
     * @param string $feature
     * @param array $arguments
     */
    public function register($feature, $arguments = array()) {
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


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                           ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/
    public function onAfterSetupThemeP99()
    {
        $this->afterSetupThemeCalled = true;
    }

}
