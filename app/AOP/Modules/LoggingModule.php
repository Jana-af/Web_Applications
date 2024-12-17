<?php

namespace App\AOP\Modules;

use App\AOP\Interceptors\LoggerInterceptor;
use Ray\Di\AbstractModule;

class LoggingModule extends AbstractModule
{

    protected function configure()
    {
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith('App\\Annotations\\Logger'),
            [LoggerInterceptor::class]
        );
    }
}
