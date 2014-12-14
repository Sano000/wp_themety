<?php

namespace Themety\Model;

use Themety\Model\Tools\MetaBox;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('model.metabox', function($app, $post)
        {
            return new MetaBox($post);
        });

        $this->app->bind('metabox.field.base', 'Themety\Content\Metabox\Field\BaseMetaField');
        $this->app->bind('metabox.field.text', 'Themety\Content\Metabox\Field\Text');
        $this->app->bind('metabox.field.textarea', 'Themety\Content\Metabox\Field\Textarea');
        $this->app->bind('metabox.field.image', 'Themety\Content\Metabox\Field\Image');
        $this->app->bind('metabox.field.checkbox', 'Themety\Content\Metabox\Field\Checkbox');
        $this->app->bind('metabox.field.group', 'Themety\Content\Metabox\Field\Group');
        $this->app->bind('metabox.field.numeric', 'Themety\Content\Metabox\Field\Numeric');
        $this->app->bind('metabox.field.post', 'Themety\Content\Metabox\Field\Post');
        $this->app->bind('metabox.field.select', 'Themety\Content\Metabox\Field\Select');

    }


}
