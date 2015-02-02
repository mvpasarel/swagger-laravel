<?php namespace Jlapp\Swaggervel;

use Illuminate\Support\ServiceProvider;
use Jlapp\Swaggervel\Installer;

use Config;

class SwaggervelServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../../config/app.php', 'swaggervel'
        );

        $this->loadViewsFrom(__DIR__.'/../../views/swaggervel', 'swaggervel');

        $this->commands(['Jlapp\Swaggervel\InstallerCommand']);
    }

    public function boot()
    {

        $this->publishes([
            __DIR__.'/../../config/app.php' => config_path('swaggervel.php'),
            __DIR__.'/../../../public' => base_path('public/packages/jlapp/swaggervel/'),
        ]);

        require_once __DIR__ . '/routes.php';
    }

}
