<?php

namespace Themety\Content;

use Exception;
use Illuminate\Support\Facades\Config;

class Taxonomy
{
    /**
     * Taxonomies
     *
     * @var array
     */
    protected $taxonomies = array();

    /**
     * Is new taxonomy can be registrated
     *
     * @var boolean
     */
    protected $taxonomyCanBeRegistrated = true;

    /**
     * Load taxonomies from config
     *
     * @return void
     */
    public function load()
    {
        $taxonomies = Config::get('contents.taxonomies', array());

        foreach ($taxonomies as $key => $taxonomy) {
            $this->add($key, $taxonomy);
        }
    }

    /**
     * Add taxonomy
     *
     * @param string $key
     * @param array $data   http://codex.wordpress.org/Function_Reference/register_taxonomy
     * @return \Themety\Content\Taxonomy
     */
    public function add($key, array $data)
    {
        if (!$this->taxonomyCanBeRegistrated) {
            throw new Exception("Too late to add a new taxonomy: $key");
        }

        $taxonomyData = array_merge(array(
            'object_type' => array(),
        ), $data);
        $this->taxonomies[$key] = $taxonomyData;

        return $this;
    }


    /**
     * Register taxonomy in Wordpress
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->taxonomies as $key => $data) {
            register_taxonomy($key, $data['object_type'], $data);
        }
        $this->taxonomyCanBeRegistrated = false;
    }
}

