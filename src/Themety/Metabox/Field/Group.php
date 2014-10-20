<?php

namespace Themety\Metabox\Field;

use Themety\Themety;
use Themety\Metabox\MetaBox;

class Group extends Base
{

    protected $defaults = array(
        'items' => array(),
    );


    /**
     * Get value
     *
     * @return array
     */
    public function get()
    {
        $result = array();

        $keys = array_keys($this->fieldData['items']);
        foreach ($keys as $key) {
            $result[$key] = $this->post->meta->{$key};
        }

        return $result;
    }


    /**
     * Render group field
     *
     * @return string
     */
    public function render()
    {
        $content = '';
        if (!empty($this->fieldData['items'] && is_array($this->fieldData['items']))) {
            foreach ($this->fieldData['items'] as $key => $itemData) {

                $itemData['id'] = $key;
                $itemContent = $this->post->meta
                    ->get($key)
                    ->setFieldData($this->post, $itemData)
                    ->render();


                $content .= $this->view('group', array(
                    'itemData' => $itemData,
                    'renderedInput' => $itemContent,
                ));
            }
        }

        return $content;
    }
}
