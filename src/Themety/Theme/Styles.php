<?php

namespace Themety\Theme;

use Themety\Base;
use Themety\Traits\AddActions;

use Themety\Themety;

class Styles extends Base {
    use AddActions;

    public function __construct()
    {
        $this->bindAddActions();
    }


    /**
     * Add styles in a frontend side
     */
    public function onWpEnqueueScripts()
    {
        $options = Themety::get('styles');
        foreach ($options as $handle => $values) {
            $data = $this->parseItem($handle, $values);
            if ($data && in_array($data['zone'], array('frontend', 'both'))) {
                $this->includeStyle($data);
            }
        }
    }


    /**
     * Add styles in the admin side
     */
    public function onAdminEnqueueScripts()
    {
        $options = Themety::get('styles');
        foreach ($options as $handle => $values) {
            $data = $this->parseItem($handle, $values);
            if ($data && in_array($data['zone'], array('backend', 'both'))) {
                $this->includeStyle($data);
            }
        }
    }


    /**
     * Add single stylesheet
     */
    protected function includeStyle($data)
    {
        wp_enqueue_style(
            $data['handle'],
            $data['src'],
            $data['deps'],
            $data['ver'],
            $data['media']
        );
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
            'media' => 'all',
            'zone' => 'frontend',
            ), $values);

        if (!empty($values['src']) && !preg_match('/^(http|https|\/)/i', $values['src'])) {
            $values['src'] = get_template_directory_uri() . '/' . $values['src'];
        }

        if (!empty($values['deps']) && !is_array($values['deps']) ) {
            $values['deps'] = explode(',', $values['deps']);
        }

        return $values;
    }
}
