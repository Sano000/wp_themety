<?php

namespace Themety\Content;

use Exception;
use Themety\Facade\Themety;
use Themety\Facade\Theme\Styles;
use Themety\Facade\Theme\Scripts;
use Themety\Model\BaseModel as Model;
use Illuminate\Support\Facades\Config;

class MetaBox
{
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


    /**
     * Load metaboxes data from config
     *
     * @return void
     */
    public function load()
    {
        $metaBoxes = Config::get('contents.meta_boxes', array());
        foreach ($metaBoxes as $key => $value) {
            $this->add($key, $value);
        }

        Scripts::add(
            'admin-themety-script',
            [
                'src' => Themety::getAssetUri('js/admin-script.js', true),
                'zone' => 'backend',
                'deps' => array('jquery', 'jquery-ui-sortable'),
            ]
        );

        Styles::add(
            'admin-themety-style',
            [
                'src' => Themety::getAssetUri('css/admin-style.css', true),
                'zone' => 'backend',
            ]
        );
    }


    /**
     * Add a new metabox
     *
     * @param string $id
     * @param array $data
     * @return \Themety\Contents
     * @throws Exception
     */
    public function add($id, array $data)
    {
        if (!$this->metaBoxCanBeRegistrated) {
            throw new Exception("Too late to add a new meta box: $id");
        }

        $field = Themety::make('metabox.field.' . $data['field_type']);
        $data['id'] = $id;
        $field->setFieldData($data);

        $this->metaBoxes[$id] = $field;

        return $this;
    }


    /**
     * Register metaboxes in WP
     *
     * @return void
     */
    public function register()
    {
        $post = Model::get(get_the_ID());

        foreach ($this->metaBoxes as $key => $field) {
            $value = $field->getFieldData();

            if ($this->isBelongsToPost($value, $post)) {
                $field->setPost($post->current());
                $field->fill();

                add_meta_box(
                    $key,
                    $value['title'],
                    $value['callback'],
                    get_post_type(),
                    $value['context'],
                    $value['priority'],
                    null //$field['callback_args']
                );
            }
        }
        $this->metaBoxCanBeRegistrated = false;
    }


    public function isBelongsToPost(array $field, $post)
    {
        $active = false;
        if (isset($field['post_type'])) {
            $postTypes = is_array($field['post_type']) ? $field['post_type'] : array($field['post_type']);
            in_array($post->post_type, $postTypes) && ($active = true);
        }

        $frontpage_id = get_option('page_on_front');
        if ($post->ID == $frontpage_id && in_array('front', $field['post_id'])) {
            $field['post_id'][] = $post->ID;
        }
        isset($field['post_id']) && in_array($post->ID, $field['post_id']) && ($active = true);

        if (
            isset($field['is_active']) &&
            is_callable($field['is_active']) &&
            call_user_func($field['is_active'], $post)
        ) {
            $active = true;
        }

        return $active;
    }


    /**
     * Save meta data
     *
     * @todo make get globals class
     */
    public static function savePost($postId)
    {
        $input = $_POST;

        if ($parentId = wp_is_post_revision($postId)) {
            $postId = $parentId;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (empty($input['themety_meta_fields']) || !is_array($input['themety_meta_fields'])) {
            return;
        }
        if (!current_user_can('edit_page', $postId)) {
            return;
        }

        $post = Model::get($postId);

        foreach ($input['themety_meta_fields'] as $data) {
            $data = unserialize(base64_decode($data));

            $key = $data['id'];
            if (array_key_exists($key, $input)) {
                $post->meta->$key->save($input[$key]);
            }
        }
    }
}
