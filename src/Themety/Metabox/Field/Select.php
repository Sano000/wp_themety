<?php

namespace Themety\Metabox\Field;

class Select extends Base
{

    protected function templateParams($data)
    {
        $options = is_callable($data['options']) ?
            call_user_func_array($data['options'], $data['options_params']) : $data['options'];
        return array('options' => $options);
    }
}
