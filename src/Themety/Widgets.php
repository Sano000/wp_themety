<?php

namespace Themety;

use Themety\Traits\AddActions;

use Themety\Themety;

class Widgets extends Base
{
    use AddActions;

    public function __construct()
    {
        $this->bindAddActions();

        Themety::set('scripts', 'admin-themety-script', array(
            'src' => $this->getAssetUri('js/admin-script.js', true),
            'zone' => 'backend',
            'deps' => array('jquery', 'jquery-ui-sortable'),
        ));
    }



    public function onWidgetsInit()
    {
        $settings = Themety::get('widgets');
        $widgets = $this->parseItems($settings);

        foreach ($widgets as $item) {
            register_widget("Themety\Widget\\" . $item);
        }
    }


    public function parseItems($data)
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
}
