<?php

namespace Themety\Content\Metabox\Field;

use Themety\Facade\Themety;

class Group extends BaseMetaField
{
    protected $itemClass = 'Themety\Content\Metabox\MultiField';

    protected $defaults = array(
        'items' => array(),
    );

    /**
     * @var array
     */
    protected $subfieldValues = [];

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


    public function fill()
    {
        $items = empty($this->fieldData['multi']['items']) ? 1 : $this->fieldData['multi']['items'];
        for($n = 0; $n < $items; $n++) {
            $data = [];
            foreach ($this->fieldData['items'] as $key=>$value) {
                $data[$key] = $this->getSubfield($key, $n);
            }
            $this->setValue($data);
        }
        $this->rewind();

        return $this;
    }


    /**
     * Render group field
     *
     * @return string
     */
    public function renderSingleField()
    {
        $content = '';
        if (isset($this->fieldData['items']) && is_array($this->fieldData['items'])) {
            foreach ($this->fieldData['items'] as $key => $itemData) {
                $itemData['id'] = $key;
                $field = $this->getSubfield($key);

                $itemContent = $field->render();

                $content .= $this->view('group', array(
                    'itemData' => $itemData,
                    'renderedInput' => $itemContent,
                ));
            }
        }

        return $content;
    }

    public function getSubfield($id, $key = null)
    {
        if (empty($this->subfieldValues[$id])) {
            $value = get_post_meta($this->post->ID, $id, true);
            is_array($value) || ($value = array($value));
            $this->subfieldValues[$id] = $value;
        }

        $field = $this->getFieldObj($id, $this->fieldData['items'][$id]);
        $field->removeAll($field);

        $key || ($key = $this->key());
        $fieldValue = isset($this->subfieldValues[$id][$key]) ? $this->subfieldValues[$id][$key] : null;

        $field->setValue($fieldValue);
        $field->rewind();

        $field->setNameAttribute($field->getData('id') . '[]');

        return $field;
    }

    public function toArray()
    {
        $result = [];
        $keys = array_keys($this->fieldData['items']);

        $this->rewind();
        foreach($keys as $key) {
            foreach($this as $n => $item) {
                $result[$n][$key] = $this->getSubfield($key)->getValue();
            }
        }

        return $result;
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

    public function __get($name)
    {
        $this->valid() || $this->rewind();

        if ($name === 'value') {
            return $this->getValue();
        }

        $keys = array_keys($this->fieldData['items']);
        if(in_array($name, $keys)) {
            return $this->getSubfield($name);
        }
        return null;
    }

    public function getValue() {
        $keys = array_keys($this->fieldData['items']);
        $result = [];
        foreach($keys as $key) {
            foreach($this as $n => $item) {
                $result[$key] = $this->getSubfield($key)->getValue();
            }
        }
        return $result;
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
