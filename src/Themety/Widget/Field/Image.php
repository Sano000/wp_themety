<?php

namespace Themety\Widget\Field;

class Image extends BaseWidgetField
{

    public function renderBeforeInput($data)
    {
        wp_enqueue_media();
        return parent::renderBeforeInput($data);
    }

    /**
     *
     */
    protected function renderMultiField($data, $values)
    {
        return parent::renderSingleField($data, $values);
    }
}
