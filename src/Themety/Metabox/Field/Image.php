<?php

namespace Themety\Metabox\Field;

class Image extends Base
{

    protected $defaults = array(
        'attributes' => array(
            'class' => 'widefat',
        ),
    );

    protected function renderMultiField($data, $values)
    {
        return parent::renderSingleField($data, $values);
    }
}
