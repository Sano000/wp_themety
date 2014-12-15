<?php

namespace Themety\Shortcodes;

use Exception;
use Illuminate\Support\Facades\Config;

class Shortcodes
{


    /**
     * Shortcodes as class => instance
     *
     * @var array
     */
    protected $shortcodes = array();


    /**
     * Is new shortcode class can be registrated
     *
     * @var boolean
     */
    protected $canBeRegistrated = true;


    public function load()
    {
        foreach ($this->getSettings() as $className) {
            $this->add($className);
        }
    }


    /**
     * Add a shortcode class
     *
     * @param string $className
     */
    protected function add($className)
    {
        if (!$this->canBeRegistrated) {
            throw new Exception("Too late to add a new shorcode class: $className");
        }

        $this->shortcodes[$className] = null;
        return $this;
    }


    protected function getSettings()
    {
        $settings = Config::get('shortcodes');
        $settings['load'] = empty($settings['load']) ? 'all' : $settings['load'];
        $settings['classes'] = array();

        $paths = array(
            Config::get('appPath') . 'Themety/Shortcodes'
        );
        foreach ($paths as $basePath) {
            $dir = realpath($basePath);
            if (!is_dir($dir)) {
                continue;
            }

            if ($settings['load'] == 'all') {
                $files = scandir($dir);
                foreach ($files as $widget) {
                    if (
                        is_file($dir . '/' . $widget)
                        && preg_match('/^(\S+)\.php$/i', $widget, $match)
                    ) {
                        $settings['classes'][] = '\Themety\Shortcodes\\' . $match[1];
                    }
                }
            } else {
                if (!empty($settings['load'])) {
                    $classes = $settings['load'];
                    if (is_string($settings['load'])) {
                        $classes = explode(',', $settings['load']);
                        $classes = array_map('trim', $classes);
                    }
                    array_merge($settings['classes'], $classes);
                }
            }
        }

        return $settings['classes'];
    }



    /**
     * Initialize shortcodes
     */
    public function register()
    {
        foreach ($this->shortcodes as $className => $value) {
            $this->shortcodes[$className] = new $className();
        }

        $this->canBeRegistrated = false;
    }
}
