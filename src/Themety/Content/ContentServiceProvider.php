<?php

namespace Themety\Content;

use Themety\BaseServiceProvider;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

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

    public function onAddMetaBoxesA2($post_type, $post)
    {
        $this->app['content.metabox']->register($post_type, $post);
    }

    public function onSavePost($postId)
    {
        $this->app['content.metabox']->savePost($postId);
    }


    /**-----------------------------------------------------------------------------
     *                                                  Ajax callbacks
     -----------------------------------------------------------------------------*/
    /**
     * Update metabox forms
     */
    public function ajaxThemetyMetaboxUpdate() {
        $pageTemplate = Input::get('page_template');
        $id = Input::get('post_id');

        $post = $id ? get_post($id) : (object) [];
        $post->template = $pageTemplate;

        set_current_screen($post->post_type);
        do_action('add_meta_boxes', $post->post_type, $post );

        $html = $this->view('content/metabox/ajax-update', [
            'post' => $post,
            'post_type' => $post->post_type
        ]);
        return Response::json([
            'html' => $html
        ]);
    }
}
