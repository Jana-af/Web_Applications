<?php

namespace App\Providers;


use Doctrine\Common\Annotations\AnnotationReader;
use Illuminate\Support\ServiceProvider;
use Ray\Di\Injector;
use App\AOP\LoggingModule;

use App\Services\FileService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FileService::class, function () {
            $module = new LoggingModule();
            $compilerPath = storage_path('ray_aop_proxies');
            $injector = new Injector($module, $compilerPath);

            return $injector->getInstance(FileService::class);
        });
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!class_exists(AnnotationReader::class)) {
            throw new \RuntimeException('Doctrine Annotations are not installed.');
        }
    }
}
