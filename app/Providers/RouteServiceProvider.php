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
        $this->mapApiRoutes();
        $this->mapAdminRoutes();
        $this->mapEnterpriseRoutes();
        $this->mapStudentRoutes();
        $this->mapWebRoutes();

        //
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
        Route::middleware(['web'])
            ->name('web.')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
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
        Route::prefix('api')
             ->middleware('api')
            ->name('api.')
             ->namespace($this->namespace.'\Api')
             ->group(base_path('routes/api.php'));
    }
    protected function mapAdminRoutes(){
        Route::prefix('admin')
            ->middleware(['web','web.check.auth','web.check.admin'])
            ->name('admin.')
            ->namespace($this->namespace.'\Admin')
            ->group(base_path('routes/admin.php'));
    }

    protected function mapEnterpriseRoutes(){
        Route::prefix('enterprise')
            ->name('enterprise.')
            ->middleware(['web','web.check.auth','web.check.enterprise'])
            ->namespace($this->namespace.'\Enterprise')
            ->group(base_path('routes/enterprise.php'));
    }

    protected function mapStudentRoutes(){
        Route::prefix('student')
            ->name('student.')
            ->middleware(['web','web.check.auth','web.check.student'])
            ->namespace($this->namespace.'\Student')
            ->group(base_path('routes/student.php'));
    }
}
