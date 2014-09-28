<?php

namespace Themety\Widget\Field;

class Number extends Text
{

    protected $defaults = array(
        'attributes' => array(
            'type' => 'number',
            'class' => 'widefat',
        )
    );
    protected $templateName = 'text';
}
