<?php

namespace Themety\Tools;

class Image
{
    /**
     * Attachment ID
     *
     * @var integer
     */
    public $id;

    /**
     * Constructor
     *
     * @param integer $id
     */
    public function __construct($id = null)
    {
        $this->setId($id);
    }


    /**
     * Set Id
     *
     * @param integer $id
     * @return \Themety\Tools\Image\Image
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Convert to string
     */
    public function __toString()
    {
        return get_attachment_link($this->id);
    }


    public function __get($name)
    {
        if (in_array($name, $this->getSizes())) {
            $img = wp_get_attachment_image_src($this->id, $name == 'original' ? null : $name);
            return (object)[
                'id'        => $this->id,
                'url'       => $img[0],
                'width'     => $img[1],
                'height'    => $img[2],
                'scale'     => $img[3],
            ];
        }
    }

    public function __call($name, $arguments)
    {
        if (in_array($name, $this->getSizes())) {
            is_array($arguments) || ($arguments = []);
            array_unshift($arguments, $name == 'original' ? null : $name);
            array_unshift($arguments, $this->id);
            return call_user_func_array('wp_get_attachment_image', $arguments);
        }
    }


    /**
     * Return allowed sizes
     *
     * @return array
     */
    public function getSizes()
    {
        $sizes = get_intermediate_image_sizes();
        array_unshift($sizes, 'original');

        return $sizes;
    }


    /**
     * Get Id
     */
    public function getValue()
    {
        return $this->id;
    }

    /**
     * Convert object to array
     *
     * @return array
     */
    public function toArray()
    {
        if (!$this->id) {
            return null;
        }

        $result = [];

        foreach ($this->getSizes() as $size) {
            $result[$size] = $this->$size;
        }

        return $result;
    }
}
