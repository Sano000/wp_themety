<?php

namespace Themety\Theme;

use Themety\Base;
use Themety\Traits\AddActions;

use Themety\Themety;

class Scripts extends Base {
    use AddActions;

    public function __construct()
    {
        $this->bindAddActions();
    }


    /**
     * Add script in a frontend side
     */
    public function onWpEnqueueScripts()
    {
        $options = Themety::get('scripts');
        foreach ($options as $handle => $values) {
            $jsData = $this->parseItem($handle, $values);
            if ($jsData && in_array($jsData['zone'], array('frontend', 'both'))) {
                $this->includeScript($jsData);
            }
        }
    }


    /**
     * Add scripts in the admin side
     */
    public function onAdminEnqueueScripts()
    {
        $options = Themety::get('scripts');
        foreach ($options as $handle => $values) {
            $jsData = $this->parseItem($handle, $values);
            if ($jsData && in_array($jsData['zone'], array('backend', 'both'))) {
                $this->includeScript($jsData);
            }
        }
    }


    /**
     * Add single script
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
            $values['src'] = Themety::get('templateUri') . '/' . $values['src'];
        }

        if (!empty($values['deps']) && !is_array($values['deps'])) {
            $values['deps'] = explode(',', $values['deps']);
        }

        $values['in_footer'] = empty($values['in_footer']) ? false : true;
        return $values;
    }
}
