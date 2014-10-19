<?php

namespace Themety\Model\Tools;

class PostModel {

    protected $model;

    /**
     * Constructor
     *
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->model = $post;
    }

    /**
     * Get model's attribute
     */
    public function __get($name)
    {
        return $this->model->$name;
    }
}
