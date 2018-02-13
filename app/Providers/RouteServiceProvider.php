<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapAppRoutes();

        $this->mapApiRoutes();

        $this->mapSecureApiRoutes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();

    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });


        // 调试相关的路由
        if ($this->app->environment('local')) {
            Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
        }
    }

    /**
     * Define the "app" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAppRoutes()
    {
        Route::group([
            'middleware' => 'app',
            'namespace' => $this->namespace,
            'prefix' => 'app',
        ], function ($router) {
            require base_path('routes/app.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSecureApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'secure_api',
        ], function ($router) {
            require base_path('routes/secure_api.php');
        });
    }
    /**
     * 定义管理员后台的路由，所有的路由都以/admin为前缀
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
            'prefix' => 'backend',
        ], function ($router) {
            require base_path('routes/admin.php');
        });
    }
}
