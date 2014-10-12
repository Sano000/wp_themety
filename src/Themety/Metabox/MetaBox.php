<?php

namespace Themety\Metabox;

use Themety\Base;
use Themety\Themety;

class MetaBox extends Base
{

    /**
     * Get meta field value
     */
    public function get($postId, $meta_name = '', $field_type = '')
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

        $data['post'] = $post;
        if (!empty($data['field_type'])) {
            $values = get_post_meta($post->ID, $data['id'], true);
            $class = 'Themety\Metabox\Field\\' . ucfirst($data['field_type']);

            $box = new $class ();
            $content = $box->render($data, $values);
        }
        echo $content;
    }

    /**
     * Save meta data
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
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

        foreach ($input['themety_meta_fields'] as $data) {
            $data = unserialize(base64_decode($data));
            $key = $data['id'];
            $class = $data['class'];
            if (array_key_exists($key, $input)) {
                $new = $input[$key];
                $old = get_post_meta($postId, $key, is_array($new) ? false : true);
                if (is_array($new)) {
                    $new = array_values(array_filter($new));
                }
                if ($new !== $old) {
                    update_post_meta($postId, $key, $new);
                }
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
