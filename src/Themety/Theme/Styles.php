<?php

namespace Themety\Theme;

use Exception;

use Themety\Base;
use Themety\Traits\AddActions;

use Themety\Themety;

class Styles extends Base {
    use AddActions;

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

    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'styles', array());
        foreach ($options as $handle => $values) {
            $this->register($handle, $values);
        }
    }



    /**
     * Register style
     *
     * @param string $handle
     * @param mixed $values
     * @return \Themety\Theme\Styles
     */
    public function register($handle, $values)
    {
        if (!$this->canBeAdded) {
            throw new Exception("Too late to register a style: $handle");
        }

        $this->styles[$handle] = $this->parseItem($handle, $values);
        return $this;
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
        return $this;
    }


    /**
     * Include all registered styles
     *
     * @param string $zone
     * @return \Themety\Theme\Styles
     */
    protected function includeAll($zone = 'frontend') {
        foreach ($this->styles as $data) {
            if ($data && in_array($data['zone'], array('frontend', 'both'))) {
                $this->includeStyle($data);
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




    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/

    /**
     * Add style in a frontend side
     */
    public function onWpEnqueueScripts()
    {
        $this->includeAll('frontend');
        $this->canBeAdded = false;
    }


    /**
     * Add styles in the admin side
     */
    public function onAdminEnqueueScripts()
    {
        $this->includeAll('backend');
        $this->canBeAdded = false;
    }
}
