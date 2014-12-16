<?php

namespace Themety\Content\Metabox\Field;

class Select extends BaseMetaField
{

    protected function templateParams()
    {
        $options = is_callable($this->fieldData['options']) ?
            call_user_func_array(
                $this->fieldData['options'],
                $this->fieldData['options_params']
            ) : $this->fieldData['options'];
        return array('options' => $options);
    }
}
