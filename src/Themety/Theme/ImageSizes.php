<?php

namespace Themety\Theme;

use Exception;
use Illuminate\Support\Facades\Config;

class ImageSizes
{
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

    public function load()
    {
        $options = Config::get('theme.image_sizes', array());
        foreach ($options as $key => $params) {
            $this->add($key, $params);
        }
    }


    /**
     * Add image size
     *
     * @param string $key
     * @param array $params
     * @return \Themety\Theme\ImageSizes
     */
    public function add($key, array $params)
    {
        if (!$this->canBeRegistered) {
            throw new Exception("Too late to add image size: $key");
        }

        array_unshift($params, $key);
        $this->imageSizes[$key] = $params;
        return $this;
    }


    /**
     * Add image sizes to WP
     */
    public function register()
    {
        foreach ($this->imageSizes as $item) {
            call_user_func_array('add_image_size', $item);
        }

        $this->canBeRegistered = false;
    }
}
