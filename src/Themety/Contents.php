<?php

namespace Themety;

use Exception;

use Themety\Traits\AddActions;

use Themety\Themety;
use Themety\Metabox\MetaBox;

class Contents extends Base
{
    use AddActions;

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
     * Meta boxes
     *
     * @var array
     */
    protected $metaBoxes = array();

    /**
     * Is a new metabox can be registrated
     *
     * @var boolean
     */
    protected $metaBoxCanBeRegistrated = true;




    public function __construct()
    {
        $this->bindAddActions();

        // get taxonomy from a settings
        $taxonomies = Themety::get('contents', 'taxonomies', array());
        foreach ($taxonomies as $key => $taxonomy) {
            $this->registerTaxonomy($key, $taxonomy);
        }


        // get post types from a settings
        $postTypes = Themety::get('contents', 'post_types', array());
        foreach ($postTypes as $key => $item) {
            $this->registerPostType($key, $item);
        }


        //  get metaboxes from a settings
        $metaBoxes = Themety::get('contents', 'meta_boxes', array());
        foreach ($metaBoxes as $key => $value) {
            $this->registerMetabox($key, $value);
        }
    }


    /**
     * Register taxonomy
     *
     * @param string $key
     * @param array $data
     * @return \Themety\Contents
     */
    public function registerTaxonomy($key, array $data)
    {
        if (!$this->taxonomyCanBeRegistrated) {
            throw new Exception("Too late to register a new taxonomy: $key");
        }

        $taxonomyData = array_merge(array(
            'object_type' => array(),
        ), $data);
        $this->taxonomies[$key] = $taxonomyData;

        return $this;
    }



    /**
     * Register a post type
     *
     * @param string $key
     * @param array $data
     * @return \Themety\Contents
     */
    public function registerPostType($key, array $data)
    {
        if (!$this->postTypeCanBeRegistrated) {
            throw new Exception("Too late to register a new post type: $key");
        }

        $postTypeData = array_merge(array(
            // @todo
        ), $data);
        $this->postTypes[$key] = $postTypeData;

        return $this;
    }


    /**
     * Register a new metabox
     *
     * @param string $key
     * @param array $data
     * @return \Themety\Contents
     * @throws Exception
     */
    public function registerMetabox($key, array $data)
    {
        if (!$this->metaBoxCanBeRegistrated) {
            throw new Exception("Too late to register a new meta box: $key");
        }

        $this->metaBoxes[$key] = MetaBox::prepareMetaBoxItem($key, $data);

        return $this;
    }




    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/
    public function onInit()
    {
        foreach ($this->taxonomies as $key => $data) {
            register_taxonomy($key, $data['object_type'], $data);
        }
        $this->taxonomyCanBeRegistrated = false;

        foreach ($this->postTypes as $key => $item) {
            register_post_type($key, $item);
        }
        $this->postTypeCanBeRegistrated = false;
    }


    public function onAddMetaBoxes()
    {
        foreach ($this->metaBoxes as $key => $value) {
            if (in_array(get_post_type(), $value['post_type'])) {
                add_meta_box(
                    $key,
                    $value['title'],
                    $value['callback'],
                    get_post_type(),
                    $value['context'],
                    $value['priority'],
                    $value['callback_args']
                );
            }

            $frontpage_id = get_option('page_on_front');
            if (get_the_ID() == $frontpage_id && in_array('front', $value['post_id'])) {
                $value['post_id'][] = get_the_ID();
            }
            if (in_array(get_the_ID(), $value['post_id'])) {
                add_meta_box(
                    $key,
                    $value['title'],
                    $value['callback'],
                    get_post_type(),
                    $value['context'],
                    $value['priority'],
                    $value['callback_args']
                );
            }
        }
        $this->metaBoxCanBeRegistrated = false;
    }



    public function onSavePost($postId)
    {
        MetaBox::savePost($postId);
    }



}