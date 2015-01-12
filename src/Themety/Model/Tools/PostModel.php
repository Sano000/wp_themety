<?php

namespace Themety\Model\Tools;

use Themety\Facade\Themety;

class PostModel
{

    /**
     * @var \WP_Post
     */
    protected $model;

    /**
     * @var \Themety\Model\Tools\MetaBox
     */
    public $meta;

    /**
     * Constructor
     *
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->model = $post;
        $this->meta = Themety::make('model.metabox', $this);
    }

    /**
     * Get model's attribute
     */
    public function __get($name)
    {
        return do_shortcode($this->model->$name);
    }


    /**
     * Convert to array
     *
     * @param array $keys
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->model as $key => $value) {
            if ($keys) {
                in_array($key, $keys) && $result[$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        $result['meta'] = $this->meta->toArray();
        return $result;
    }


    /**
     * Convert to string
     */
    public function __toString()
    {
        return json_encode($this->toArray($keys));
    }
}
