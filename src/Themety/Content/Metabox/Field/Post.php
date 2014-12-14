<?php

namespace Themety\Content\Metabox\Field;

use Themety\Themety;

class Post extends BaseMetaField
{

    protected function templateParams($data)
    {
        $options = Themety::app()->posts->getList($data['options']);
        return array('options' => $options);
    }
}
