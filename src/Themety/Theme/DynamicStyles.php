<?php

namespace Themety\Theme;

use Exception;

use Themety\Base;
use Themety\Traits\AddActions;
use Themety\Themety;

use Themety\Facade\Routes as RoutesFacade;
use Themety\Facade\Theme\Styles as StylesFacade;

class DynamicStyles extends Base {
    use AddActions;

    /**
     * Styles
     *
     * @var array
     */
    protected $styles = array();

    /**
     * Is a new item can be registrated
     *
     * @var boolean
     */
    protected $canBeRegistrated = true;

    public function __construct()
    {
        $this->bindAddActions();

        $options = Themety::get('theme', 'dynamic_styles', array());
        foreach ($options as $key => $value) {
            $this->register($key, $value);
        }
    }


    /**
     * Register a new dynamic style
     *
     * @param string $key
     * @param string $data
     * @return \Themety\Theme\Menu
     */
    public function register($key, array $data)
    {
        if (!$this->canBeRegistrated) {
            throw new Exception("Too late to register style: $key");
        }

        $this->styles[$key] = $this->parse($key, $data);
        return $this;
    }



    public function parse($key, array $data)
    {
        $item = array_merge(array(
            'handle' => $key,
            'src' => '',
            'file' => '',
            'show' => true,
        ), $data);

        return $item;
    }


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  Actions
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Register rewrite rules
     */
    public function onInit()
    {
        foreach ($this->styles as $key => $value) {
            RoutesFacade::register(
                $key,
                array(
                    'rule' => $value['src'],
                    'callback' => function() use ($key, $value) {
                        $file = $value['file'];
                        if (!is_file($file)) {
                            throw new Exception("Style file not exists: $file ($key)");
                        }

                        header("Content-Type: text/css");
                        include($file);
                        die();
                    },
                    'after' => 'top',
                )
            );

            is_callable($value['show']) && ($value['show'] = call_user_func($value['show']));
            $value['src'] = site_url($value['src']);
            $value['show'] && StylesFacade::register($key, $value);
        }

        $this->canRegister = false;
    }

}
