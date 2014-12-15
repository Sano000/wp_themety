<?php

namespace Themety\Content;

use Themety\BaseServiceProvider;

class ContentServiceProvider extends BaseServiceProvider
{

    public function register()
    {

        $this->app->bindShared('content.post_type', function() {
            return new PostType;
        });

        $this->app->bindShared('content.taxonomy', function() {
            return new Taxonomy;
        });

        $this->app->bindShared('content.metabox', function() {
            return new MetaBox;
        });


        $this->app['content.taxonomy']->load();
        $this->app['content.post_type']->load();
        $this->app['content.metabox']->load();
    }


    /**--------------------------------------------------------------------------------
     *                                                      WP Events
     --------------------------------------------------------------------------------*/
    public function onInit()
    {
        $this->app['content.taxonomy']->register();
        $this->app['content.post_type']->register();
    }

    public function onAddMetaBoxes()
    {
        $this->app['content.metabox']->register();
    }

    public function onSavePost($postId)
    {
        $this->app['content.metabox']->savePost($postId);
    }
}
