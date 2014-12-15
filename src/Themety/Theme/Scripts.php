<?php

namespace Themety\Theme;

use Exception;
use Illuminate\Support\Facades\Config;

class Scripts
{

    /**
     * Scripts
     *
     * @var array
     */
    protected $scripts = array();


    /**
     * Is a script can be registered
     *
     * @var boolean
     */
    protected $canBeAdded = true;


    public function load()
    {
        $options = Config::get('theme.scripts', array());
        foreach ($options as $handle => $values) {
            $this->add($handle, $values);
        }
    }


    /**
     * Add script
     *
     * @param string $handle
     * @param mixed $values
     * @return \Themety\Theme\Scripts
     */
    public function add($handle, $values)
    {
        if (!$this->canBeAdded) {
            throw new Exception("Too late to add a script: $handle");
        }

        $this->scripts[$handle] = $this->parseItem($handle, $values);
        return $this;
    }


    /**
     * Include all registered scripts
     *
     * @param string $zone
     * @return \Themety\Theme\Scripts
     */
    public function register($zone = 'frontend')
    {
        foreach ($this->scripts as $jsData) {
            if ($jsData && in_array($jsData['zone'], array($zone, 'both'))) {
                $this->registerScript($jsData);
            }
        }

        $this->canBeAdded = false;
        return $this;
    }


    /**
     * Include script
     */
    protected function registerScript($jsData)
    {
        wp_enqueue_script(
            $jsData['handle'],
            $jsData['src'],
            $jsData['deps'],
            $jsData['ver'],
            $jsData['in_footer']
        );
        foreach ($jsData['params'] as $param => $values) {
            is_callable($values) && ( $values = call_user_func($values));
            wp_localize_script($jsData['handle'], $param, $values);
        }
        return $this;
    }


    /**
     * Parse settings item
     */
    protected function parseItem($handle, $values)
    {
        if (!is_array($values)) {
            $values = array('src' => $values);
        }

        $values = array_merge(array(
            'handle' => $handle,
            'src' => false,
            'deps' => '',
            'ver' => false,
            'in_footer' => false,
            'zone' => 'frontend',
            'params' => array(),
            ), $values);

        if (!empty($values['src']) && !preg_match('/^(http|https|\/)/i', $values['src'])) {
            $values['src'] = Config::get('templateUri') . '/' . $values['src'];
        }

        if (!empty($values['deps']) && !is_array($values['deps'])) {
            $values['deps'] = explode(',', $values['deps']);
        }

        $values['in_footer'] = empty($values['in_footer']) ? false : true;
        return $values;
    }
}
