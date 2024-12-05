<?php

namespace App\AOP;

use Ray\Di\AbstractModule;

class LoggingModule extends AbstractModule
{

    protected function configure()
    {
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith('App\\AOP\\Logger'),
            [LoggerInterceptor::class]
        );
    }
}
