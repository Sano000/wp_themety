<?php

namespace Themety\Widget;

use Exception;
use Themety\Facade\Themety;
use Themety\Facade\Theme\Styles;
use Themety\Facade\Theme\Scripts;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class Widgets
{
    /**
     * Widgets class names
     *
     * @var array
     */
    protected $widgets = array();


    /**
     * Is widget can be registered
     *
     * @var boolean
     */
    protected $canBeRegistrated = true;



    /**
     * Load widgets from config
     *
     * @return void
     */
    public function load()
    {
        $data = $this->getSettings();

        foreach ($data as $widget) {
            $this->add($widget);
        }

        Scripts::add(
            'admin-themety-script',
            [
                'src' => Themety::getAssetUri('js/admin-script.js', true),
                'zone' => 'backend',
                'deps' => array('jquery', 'jquery-ui-sortable'),
            ]
        );

        Styles::add(
            'admin-themety-style',
            [
                'src' => Themety::getAssetUri('css/admin-style.css', true),
                'zone' => 'backend',
            ]
        );
    }

    /**
     * Add Widgets
     *
     * @param string $className
     * @return \Themety\Widget\Widgets
     */
    public function add($className)
    {
        if (!$this->canBeRegistrated) {
            throw new Exception("Too late to add a new widget class: $className");
        }

        $this->widgets[] = $className;
        return $this;
    }


    /**
     * Register widgets in Wordpress
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->widgets as $className) {
            register_widget($className);
        }

        $this->canBeRegistrated = false;
    }



    protected function getSettings()
    {
        $data = Config::get('widgets', array());

        $data['load'] = empty($data['load']) ? 'all' : $data['load'];
        $classes= array();

        $paths = array(
            Config::get('appPath') . 'Themety/Widget',
        );
        foreach ($paths as $basePath) {
            if (!is_dir($basePath)) {
                continue;
            }
            if ($data['load'] == 'all') {
                $files = File::files($basePath);
                foreach ($files as $widget) {
                    if (preg_match('/^.+\/(\S+?)\.php$/i', $widget, $match)) {
                        $name = 'Themety\Widget\\' . $match[1];
                        $classes[] = $name;
                    }
                }
            } else {
                if (!empty($data['load'])) {
                    $widgets = explode(',', $data['load']);
                    $classes = array_map('trim', $widgets);
                }
            }
        }

        return $classes;
    }
}

