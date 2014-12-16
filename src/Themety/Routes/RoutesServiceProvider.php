<?php

namespace Themety\Routes;

use Themety\BaseServiceProvider;

class RoutesServiceProvider extends BaseServiceProvider
{

    public function register()
    {

        $this->app->bindShared('routes', function() {
            return new Routes;
        });

        $this->app['routes']->load();
    }


    /**--------------------------------------------------------------------------------
     *                                                      WP Events
     --------------------------------------------------------------------------------*/
    public function onInitP99()
    {
        $this->app['routes']->register();
    }

    public function onParseRequest($wp)
    {
        $this->app['routes']->parseQuery($wp);
    }

    public function onQueryVars($vars)
    {
        return $this->app['routes']->updateQueryVars($vars);
    }
}
