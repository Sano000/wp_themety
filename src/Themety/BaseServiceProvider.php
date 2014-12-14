<?php

namespace Themety;

use Themety\Traits\AddActions;
use Illuminate\Support\ServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    use AddActions;

    public function __construct($app)
    {
        $this->bindAddActions();
        parent::__construct($app);
    }
}
