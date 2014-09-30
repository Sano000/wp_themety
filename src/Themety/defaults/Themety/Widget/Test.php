<?php

namespace Themety\Widget;

class Test extends Base
{

    protected $widgetId = 'test';
    protected $widgetName = 'Test';
    protected $widgetOptions = array(
        'description' => 'Widget Example',
    );
    protected $fields = array(
        'field' => array(
            'type' => 'number',
            'label' => 'Test Field',
            'default' => null,
            'multi' => array('items' => 3),
        ),
        'field3' => array(
            'type' => 'custom',
            'label' => 'Test Field3',
            'default' => null,
        ),
        'field2' => array(
            'type' => 'image',
            'label' => 'Image',
            'default' => null,
            'multi' => false,
        )
    );
}
