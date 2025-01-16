<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ray\Di\Injector;
use Illuminate\Support\Facades\File;

class InterceptorModulesProvider extends ServiceProvider
{
    private $serviceNamespace = 'App\Services';
    private $moduleNamespace = 'App\AOP\Modules';


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $servicePath = app_path('Services');
        $modulePath = app_path('AOP\Modules');
        $services = $this->getClassesInNamespace($this->serviceNamespace, $servicePath);
        $modules = $this->getClassesInNamespace($this->moduleNamespace, $modulePath);

        foreach ($services as $service) {
            $this->app->singletonIf($service, function () use ($modules, $service) {
                $modulesToBind = [];
                foreach ($modules as $module) {
                    array_push($modulesToBind,new $module());
                }
                $injector = new Injector($modulesToBind);
                return $injector->getInstance($service);
            });
        }
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}

    /**
     * Get all classes in a specific namespace and path
     *
     * @param string $namespace The base namespace
     * @param string $path The directory path
     * @return array
     */
    protected function getClassesInNamespace($namespace, $path)
    {
        return collect(File::allFiles($path))
            ->map(function ($file) use ($namespace) {
                $relativePath = $file->getRelativePathname();
                $class = sprintf(
                    '%s\%s',
                    $namespace,
                    str_replace(['/', '.php'], ['\\', ''], $relativePath)
                );

                return class_exists($class) ? $class : null;
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
