<?php

namespace Themety\Theme;

use Themety\BaseServiceProvider;

class ThemeServiceProvider extends BaseServiceProvider
{

    public function register()
    {

        $this->app->bindShared('theme.theme_support', function() {
            return new ThemeSupport;
        });

        $this->app->bindShared('theme.styles', function() {
            return new Styles;
        });

        $this->app->bindShared('theme.scripts', function() {
            return new Scripts;
        });

        $this->app->bindShared('theme.sidebar', function() {
            return new Sidebar;
        });

        $this->app->bindShared('theme.menu', function() {
            return new Menu;
        });

        $this->app->bindShared('theme.image_sizes', function() {
            return new Menu;
        });


        $this->app['theme.theme_support']->load();
        $this->app['theme.styles']->load();
        $this->app['theme.scripts']->load();
        $this->app['theme.sidebar']->load();
        $this->app['theme.menu']->load();
        $this->app['theme.image_sizes']->load();
    }


    /**--------------------------------------------------------------------------------
     *                                                      WP Events
     --------------------------------------------------------------------------------*/
    public function onInit()
    {
        $this->app['theme.menu']->register();
    }

    public function onWpEnqueueScripts()
    {
        $this->app['theme.styles']->register('frontend');
        $this->app['theme.scripts']->register('frontend');
    }

    public function onAdminEnqueueScripts()
    {
        $this->app['theme.styles']->register('backend');
        $this->app['theme.scripts']->register('backend');
    }

    public function onWidgetsInit()
    {
        $this->app['theme.sidebar']->register();
    }

    public function onAfterSetupTheme()
    {
        $this->app['theme.image_sizes']->register();
    }

    public function onAfterSetupThemeP99()
    {
        $this->app['theme.theme_support']->register();
    }

}
