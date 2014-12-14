<?php

namespace Themety\Shortcodes;

class Widgets extends BaseShortcode
{

    public function scSidebar($attr = array(), $content = null)
    {
        $attr = shortcode_atts(
            array('id' => '', 'class' => ''),
            $attr
        );
        return $this->view('widgets/sidebar', $attr);
    }
}
