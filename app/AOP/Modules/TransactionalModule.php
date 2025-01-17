<?php

namespace App\AOP\Modules;

use App\Annotations\Transactional;
use App\AOP\Interceptors\TransactionalInterceptor;
use Ray\Di\AbstractModule;

class TransactionalModule extends AbstractModule
{

    protected function configure()
    {
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Transactional::class),
            [TransactionalInterceptor::class]
        );
    }
}
