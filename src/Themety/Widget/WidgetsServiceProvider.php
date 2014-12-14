<?php

namespace Themety\Widget;

use Themety\BaseServiceProvider;

class WidgetsServiceProvider extends BaseServiceProvider
{

    public function register()
    {

        $this->app->bindShared('widgets', function() {
            return new Widgets;
        });

        $this->app['widgets']->load();
    }




    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Initialize shortcodes
     */
    public function onWidgetsInit()
    {
        $this->app['widgets']->register();
    }
}
