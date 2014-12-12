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

    protected function renderMultiField()
    {
        return parent::renderSingleField();
    }

    public function toArray()
    {
        return $this->current()->toArray();
    }
}
