<?php

namespace Themety\Shortcodes;

use Themety\BaseServiceProvider;

class ShortcodeServiceProvider extends BaseServiceProvider
{

    public function register()
    {

        $this->app->bindShared('shortcode', function() {
            return new Shortcodes;
        });

        $this->app['shortcode']->load();
    }


    public function onInit()
    {
        $this->app['shortcode']->register();
    }
}
