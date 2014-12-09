<?php

namespace Themety\Metabox;

use Themety\Model\Base as Model;
use Themety\Base;
use Themety\Themety;
use Themety\Model\Tools\PostModel;

class MetaBox extends Base
{

    /**
     * Get meta field value
     */
    public static function get($postId, $meta_name = '', $field_type = '')
    {
        if (!is_numeric($postId)) {
            $field_type = $meta_name;
            $meta_name = $postId;
            $postId = get_the_ID();
        }

        $metaboxes = Themety::app()->config->get('meta_boxes');
        if (array_key_exists($meta_name, $metaboxes)) {
            $field_type = $metaboxes[$meta_name]['field_type'];
        }

        if ($field_type) {
            $class = 'themety\metaboxes\\' . ucfirst($field_type);
            $box = new $class ();
            $value = $box->get($postId, $meta_name);
        } else {
            $value = get_post_meta($postId, $meta_name, true);
        }

        return $value;
    }

    /**
     * Render form callback
     */
    public function render($post, $meta)
    {
        $data = $meta['args'];
        if (empty($data)) {
            return false;
        }
        $postModel = new PostModel($post);

        $data['post'] = $postModel;
        $content = '';
        if (!empty($data['field_type'])) {
            $content = $postModel->meta
                ->get($data['id'])
                ->setFieldData($postModel, $data)
                ->render();
        }
        echo $content;
    }

    /**
     * Save meta data
     *
     * @todo make get globals class
     */
    public static function savePost($postId)
    {
        $input = $_POST;
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

    public static function getDefaults($id, array $data)
    {
        return array_merge(array(
            'id' => $id,
            'title' => '',
            'callback' => array('Themety\Metabox\MetaBox', 'render'),
            'callback_args' => null,
            'context' => 'advanced',
            'priority' => 'default',
            'post_type' => false,
            'post_id' => false,
            'description' => '',
            'field_type' => 'text',
            'attributes' => array(),
            'multi' => false,
            ), $data);
    }


    /**
     * Prepare Meta box
     */
    public static function prepareMetaBoxItem($key, array $data)
    {
        $result = self::getDefaults($key, $data);

        if (!is_array($result['post_type'])) {
            $result['post_type'] = array($result['post_type']);
        }

        if (!is_array($result['post_id'])) {
            $result['post_id'] = array($result['post_id']);
        }

        if (empty($result['callback_args'])) {
            $result['callback_args'] = $result;
        }
        return $result;
    }
}
