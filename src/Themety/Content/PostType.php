<?php

namespace Themety\Content;

use Exception;
use Illuminate\Support\Facades\Config;

class PostType
{
    /**
     * Post types
     *
     * @var array
     */
    protected $postTypes = array();

    /**
     * Is new post type can be registrated
     *
     * @var boolean
     */
    protected $postTypeCanBeRegistrated = true;



    /**
     * Load post types data from config
     *
     * @return void
     */
    public function load()
    {
        $postTypes = Config::get('contents.post_types', array());
        foreach ($postTypes as $key => $item) {
            $this->add($key, $item);
        }
    }

    /**
     * Register a post type
     *
     * @param string $key
     * @param array $data   http://codex.wordpress.org/Function_Reference/register_post_type
     * @return \Themety\Contents\PostType
     */
    public function add($key, array $data)
    {
        if (!$this->postTypeCanBeRegistrated) {
            throw new Exception("Too late to add a new post type: $key");
        }

        $postTypeData = array_merge(array(
            // @todo
        ), $data);
        $this->postTypes[$key] = $postTypeData;

        return $this;
    }


    /**
     * Register post types in Wordpress
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->postTypes as $key => $item) {
            register_post_type($key, $item);
        }
        $this->postTypeCanBeRegistrated = false;
    }
}
