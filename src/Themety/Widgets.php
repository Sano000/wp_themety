<?php

namespace Themety;

use Exception;

use Themety\Traits\AddActions;

use Themety\Themety;

class Widgets extends Base
{
    use AddActions;


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

    public function __construct()
    {
        $this->bindAddActions();

        Themety::set('scripts', 'admin-themety-script', array(
            'src' => $this->getAssetUri('js/admin-script.js', true),
            'zone' => 'backend',
            'deps' => array('jquery', 'jquery-ui-sortable'),
        ));

        $settings = Themety::get('widgets');
        $widgets = $this->getSettings($settings);

        foreach ($widgets as $item) {
            $this->register("Themety\Widget\\" . $item);
        }
    }



    /**
     * Register widget class name
     *
     * @param string $className
     */
    public function register($className)
    {
        if (!$this->canBeRegistrated) {
            throw new Exception("Too late to register a new widget class: $className");
        }

        $this->widgets[] = $className;
        return $this;
    }


    protected function getSettings(array $data)
    {
        $data['load'] = empty($data['load']) ? 'all' : $data['load'];
        $data['widgets'] = array();

        $paths = array(
            Themety::get('core', 'appPath') . 'Themety/Widget',
        );
        foreach ($paths as $basePath) {
            if (!is_dir($basePath)) {
                continue;
            }
            if ($data['load'] == 'all') {
                $files = scandir($basePath);
                foreach ($files as $widget) {
                    if (preg_match('/^(\S+)\.php$/i', $widget, $match)) {
                        $data['widgets'][] = $match[1];
                    }
                }
            } else {
                if (!empty($data['load'])) {
                    $widgets = explode(',', $data['load']);
                    $data['widgets'] = array_map('trim', $widgets);
                }
            }
        }
        return $data['widgets'];
    }


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  ACTIONS
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Initialize shortcodes
     */
    public function onWidgetsInit()
    {
        foreach ($this->widgets as $className) {
            register_widget($className);
        }

        $this->canBeRegistrated = false;
    }
}
