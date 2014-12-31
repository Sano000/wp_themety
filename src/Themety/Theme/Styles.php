<?php

namespace Themety\Theme;

use Exception;
use Illuminate\Support\Facades\Config;

class Styles
{

    /**
     * Styles
     *
     * @var array
     */
    protected $styles = array();


    /**
     * Is a style can be registered
     *
     * @var boolean
     */
    protected $canBeAdded = true;


    public function load()
    {
        $options = Config::get('theme.styles', array());
        foreach ($options as $handle => $values) {
            $this->add($handle, $values);
        }
    }



    /**
     * Add style
     *
     * @param string $handle
     * @param mixed $values   http://codex.wordpress.org/Function_Reference/wp_enqueue_style
     * @return \Themety\Theme\Styles
     */
    public function add($handle, $values)
    {
        if (!$this->canBeAdded) {
            throw new Exception("Too late to add a style: $handle");
        }

        $this->styles[$handle] = $this->parseItem($handle, $values);
        return $this;
    }


    /**
     * Include all registered styles
     *
     * @param string $zone
     */
    public function register($zone = 'frontend')
    {
        foreach ($this->styles as $data) {
            if ($data && in_array($data['zone'], array($zone, 'both'))) {
                $this->registerStyle($data);
            }
        }
        $this->canBeAdded = false;
    }



    /**
     * Add single stylesheet
     */
    protected function registerStyle($data)
    {
        wp_enqueue_style(
            $data['handle'],
            $data['src'],
            $data['deps'],
            $data['ver'],
            $data['media']
        );
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
            'media' => 'all',
            'zone' => 'frontend',
            ), $values);

        if (!empty($values['src']) && !preg_match('/^(http|https|\/)/i', $values['src'])) {
            $values['src'] = Config::get('templateUri') . '/' . $values['src'];
        }

        if (!empty($values['deps']) && !is_array($values['deps'])) {
            $values['deps'] = explode(',', $values['deps']);
        }

        return $values;
    }
}
