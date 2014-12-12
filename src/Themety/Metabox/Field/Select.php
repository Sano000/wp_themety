<?php

namespace Themety\Metabox\Field;

class Select extends Base
{

    protected function templateParams()
    {
        $options = is_callable($this->fieldData['options']) ?
            call_user_func_array($this->fieldData['options'], $this->fieldData['options_params']) : $this->fieldData['options'];
        return array('options' => $options);
    }
}
