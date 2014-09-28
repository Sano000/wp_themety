<?php

namespace Themety\Metabox\Field;

use Themety\Themety;

class Post extends Base
{

    protected function templateParams($data)
    {
        $options = Themety::app()->posts->getList($data['options']);
        return array('options' => $options);
    }
}
