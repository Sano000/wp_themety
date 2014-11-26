<?php

namespace Themety\Metabox\Field;

class Image extends Base
{
    protected $itemClass = 'Themety\Tools\Image';

    protected $defaults = array(
        'attributes' => array(
            'class' => 'widefat',
        ),
    );

    protected function renderMultiField($data, $values)
    {
        return parent::renderSingleField($data, $values);
    }

    public function toArray()
    {
        return $this->current()->toArray();
    }
}
