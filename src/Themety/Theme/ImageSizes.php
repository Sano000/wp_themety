<?php

namespace Themety\Theme;

use Exception;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

class ImageSizes extends Base {
    use AddActions;

    /**
     * Image sizes
     *
     * @var array
     */
    protected $imageSizes = array();


    /**
     * Is new image sizes can be registered
     *
     * @var boolean
     */
    protected $canBeRegistered = true;

    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'image_sizes', array());
        foreach ($options as $key => $params) {
            $this->register($key, $params);
        }
    }


    /**
     * Register image size
     *
     * @param string $key
     * @param array $params
     * @return \Themety\Theme\ImageSizes
     */
    public function register($key, array $params)
    {
        if (!$this->canBeRegistered) {
            throw new Exception("Too late to register image size: $key");
        }

        array_unshift($params, $key);
        $this->imageSizes[$key] = $params;
        return $this;
    }


    /**-----------------------------------------------------------------------------------------------------------------
     *
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Add image sizes
     */
    public function onAfterSetupTheme()
    {
        foreach ($this->imageSizes as $item) {
            call_user_func_array('add_image_size', $item);
        }

        $this->canBeRegistered = false;
    }
}
