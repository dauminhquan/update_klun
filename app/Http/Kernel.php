<?php

namespace App\Http;

use App\Http\Middleware\ApiCheckAdmin;
use App\Http\Middleware\ApiCheckEnterprise;
use App\Http\Middleware\ApiCheckStudent;
use App\Http\Middleware\CheckAccepUser;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\RemoveSession;
use App\Http\Middleware\WebCheckAdmin;
use App\Http\Middleware\WebCheckEnterprise;
use App\Http\Middleware\WebCheckLogin;
use App\Http\Middleware\WebCheckStudent;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            'throttle:60,1',
            'bindings',
        ],

    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'api.check.admin' => ApiCheckAdmin::class,
        'api.check.enterprise' => ApiCheckEnterprise::class,
        'api.check.student' => ApiCheckStudent::class,
        'web.check.admin' => WebCheckAdmin::class,
        'web.check.enterprise' => WebCheckEnterprise::class,
        'web.check.student' => WebCheckStudent::class,
        'web.check.login' => WebCheckLogin::class,
        'web.check.auth' => CheckAuthentication::class,
        'remove-session' => RemoveSession::class,
        'accept' => CheckAccepUser::class
    ];
}
