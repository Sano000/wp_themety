<?php

namespace Themety\Model\Tools;

use Themety\Facade\Themety;
use Illuminate\Support\Facades\Config;
use Themety\Metabox\Field\Base as BaseField;
use Themety\Facade\Metabox as ContentMetaBox;

class MetaBox
{

    /**
     * Fields
     *
     * @var array
     */
    protected $fields = array();


    /**
     * Base post
     *
     * @var \Themety\Model\Tools\PostModel
     */
    protected $post;

    /**
     * Meta Fields
     *
     * @var
     */
    protected $metaFields;


    public function __construct(&$post)
    {
        $this->post = $post;
        $this->initScheme();
    }


    /**
     * Get field value
     *
     * @param string $metaName
     * @retrun mixed Field value
     */
    public function __get($metaName)
    {
        return $this->get($metaName);
    }


    /**
     * Get field object
     *
     * @param string $metaName
     * @return  Themety\Metabox\Field\Base
     */
    public function get($metaName)
    {
        $field = empty($this->fields[$metaName]) ? $this->getFieldObj($metaName) : $this->fields[$metaName];
        return $field;
    }


    /**
     * Convert meta fields to array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this->fields as $key => $meta) {
            $result[$key] = $meta instanceof BaseField ? $meta->toArray() : $meta;
        }
        return $result;
    }
    /**-------------------------------------------------------------------------------------------------
     *                                                                           Utils
     --------------------------------------------------------------------------------------------------*/
    /**
     * Initialize fields scheme
     */
    protected function initScheme()
    {
        $items = Config::get('contents.meta_boxes', array());
        $this->scanSettings($items);

        return $this;
    }


    /**
     * Scan field settings recursively
     *
     * @param array $items
     * @return \Themety\Model\Tools\MetaBox
     */
    protected function scanSettings(array $items)
    {
        foreach ($items as $key => $value) {
            if (
                ContentMetaBox::isBelongsToPost($value, $this->post, $this->post->post_type)
            ) {
                $this->fields[$key] = $this->getFieldObj($key, $value);
                if (isset($value['items']) && is_array($value['items'])) {
                    $this->scanSettings($value['items']);
                }
            }
        }
        return $this;
    }


    /**
     * Get field object
     *
     * @param string $id
     * @param array $fieldData
     * @return \Themety\Model\Tools\Base
     */
    protected function getFieldObj($id, array $fieldData = array())
    {
        if (empty($fieldData)) {
            $alias = 'metabox.field.base';
        } else {
            $alias = 'metabox.field.' . $fieldData['field_type'];
        }
        $fieldData['id'] = $id;

        $field = Themety::make($alias);
        $field->setPost($this->post);
        $field->setFieldData($fieldData);
        $field->fill();

        return $field;
    }
}
