<?php

namespace Themety\Metabox\Field;

use Themety\Themety;
use Themety\Metabox\MetaBox;

class Group extends Base
{

    protected $defaults = array(
        'items' => array(),
    );

    public function get($postId, $meta_name)
    {
        $result = array();
        $metaboxes = Themety::app()->config->get('meta_boxes');
        if (array_key_exists($meta_name, $metaboxes)) {
            $keys = array_keys($metaboxes[$meta_name]['items']);
            $values = get_post_custom($postId);
            foreach ($keys as $key) {
                $val = array_key_exists($key, $values) ? reset($values[$key]) : false;
                $result[$key] = $metaboxes[$meta_name]['items'][$key]['multi'] ? unserialize($val) : $val;
            }
        }

        return $result;
    }

    public function render($data, $value)
    {
        $content = '';
        if (!empty($data['items'])) {
            foreach ($data['items'] as $key => $itemData) {

                $itemData = MetaBox::getDefaults($key, $itemData);
                $values = get_post_meta($data['post']->ID, $itemData['id'], true);

                $class = 'Themety\Metabox\Field\\' . ucfirst($itemData['field_type']);
                $box = new $class ();

                $content .= $this->view('group', array(
                    'itemData' => $itemData,
                    'renderedInput' => $box->render($itemData, $values),
                ));
            }
        }

        return $content;
    }
}
