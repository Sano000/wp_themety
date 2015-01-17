<?php

namespace Themety;

use Themety\Traits\View;
use Themety\Traits\AddActions;
use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    use View,
        AddActions;

    protected $templatesPath = 'Views';
    protected $templatesBasePath;

    public function __construct($app)
    {
        $this->templatesBasePath = __DIR__;

        $this->bindAddActions();
        parent::__construct($app);
    }
}
