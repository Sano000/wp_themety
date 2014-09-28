<?php

namespace Themety;

use Themety\Traits\AddActions;

use Themety\Themety;
use Themety\Metabox\MetaBox;

class Contents extends Base
{
    use AddActions;

    public function __construct()
    {
        $this->bindAddActions();
    }



    public function onInit()
    {
        // Initialize Taxonomies
        $taxonomies = Themety::get('contents', 'taxonomies', array());
        foreach ($taxonomies as $key => $taxonomy) {
            $objects = empty($taxonomy['object_type']) ? array() : $taxonomy['object_type'];
            register_taxonomy($key, $objects, $taxonomy);
        }

        // Initialize post types
        $postTypes = Themety::get('contents', 'post_types', array());
        foreach ($postTypes as $key => $item) {
            register_post_type($key, $item);
        }
    }


    public function onAddMetaBoxes()
    {
        $metaBoxes = Themety::get('contents', 'meta_boxes', array());
        foreach ($metaBoxes as $key => $value) {
            $value = MetaBox::prepareMetaBoxItem($key, $value);

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
    }



    public function onSavePost($postId)
    {
        MetaBox::savePost($postId);
    }



}