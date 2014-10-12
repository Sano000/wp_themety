<?php

namespace Themety\Theme;

use Exception;

use Themety\Base;
use Themety\Traits\AddActions;

use Themety\Themety;

class Scripts extends Base {
    use AddActions;

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

    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'scripts', array());
        foreach ($options as $handle => $values) {
            $this->register($handle, $values);
        }
    }


    /**
     * Register script
     *
     * @param string $handle
     * @param mixed $values
     * @return \Themety\Theme\Scripts
     */
    public function register($handle, $values)
    {
        if (!$this->canBeAdded) {
            throw new Exception("Too late to register a script: $handle");
        }

        $this->scripts[$handle] = $this->parseItem($handle, $values);
        return $this;
    }


    /**
     * Include script
     */
    protected function includeScript($jsData)
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
     * Include all registered scripts
     *
     * @param string $zone
     * @return \Themety\Theme\Scripts
     */
    protected function includeAll($zone = 'frontend') {
        foreach ($this->scripts as $jsData) {
            if ($jsData && in_array($jsData['zone'], array('frontend', 'both'))) {
                $this->includeScript($jsData);
            }
        }

        return $this;
    }


    /**
     * Parse settings item
     */
    protected function parseItem($handle, $values) {
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
            $values['src'] = Themety::get('core', 'templateUri') . '/' . $values['src'];
        }

        if (!empty($values['deps']) && !is_array($values['deps'])) {
            $values['deps'] = explode(',', $values['deps']);
        }

        $values['in_footer'] = empty($values['in_footer']) ? false : true;
        return $values;
    }




    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/

    /**
     * Add script in a frontend side
     */
    public function onWpEnqueueScripts()
    {
        $this->includeAll('frontend');
        $this->canBeAdded = false;
    }


    /**
     * Add scripts in the admin side
     */
    public function onAdminEnqueueScripts()
    {
        $this->includeAll('backend');
        $this->canBeAdded = false;
    }
}
