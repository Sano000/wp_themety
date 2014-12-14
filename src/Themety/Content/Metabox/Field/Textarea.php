<?php

namespace Themety\Content\Metabox\Field;

class Textarea extends BaseMetaField
{

    protected $defaults = array(
        'attributes' => array(
            'class' => 'widefat',
            'rows' => 10,
        ),
    );
}
