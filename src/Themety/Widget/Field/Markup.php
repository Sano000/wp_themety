<?php

namespace Themety\Widget\Field;

class Markup extends Base
{

    protected $defaults = array(
        'markup' => '<p>Use "markup" attribute to set custom markup</p>',
    );

    public function render($data, $instance)
    {
        $data = $this->getDefaults($this->data);
        return $data['markup'];
    }
}
