<?php

namespace YK\Basic\Providers;


use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use YK\Basic\Console\CreateDatabaseCommand;
use YK\Basic\Console\InstallCommand;
use Laravel\Passport\Bridge\PersonalAccessGrant;
use League\OAuth2\Server\AuthorizationServer;
use Route;

class BasicServiceProvider extends ServiceProvider
{
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.operationLog'        => \YK\Basic\Middleware\LogOperation::class,
        'auth' => \SMartins\PassportMultiauth\Http\Middleware\MultiAuthenticate::class,
        'oauth.providers' => \SMartins\PassportMultiauth\Http\Middleware\AddCustomProvider::class,
        'admin.permission' => \YK\Basic\Middleware\Authenticate::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'api' => [
            'admin.operationLog',
            'admin.permission',
        ],
    ];
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../../config/admin.php' => config_path('admin.php'),
            ], 'config');

            $path = version_compare(app()->version(), '5.7.0', '>=')
                ? base_path('resources/js')
                : base_path('resources/assets/js');

            $this->publishes([
                __DIR__.'/../../resources/js' => $path
            ]);

            $this->publishes([
                __DIR__.'/../../views' => base_path('resources/views')
            ]);


            //$this->registerMigrations();
            $this->commands([
                InstallCommand::class,
            ]);



        }

        $this->registerRouteMiddleware();
        $this->registerRouter();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }


    /**
     * 注册路由
     *
     * @author moell
     */
    private function registerRouter()
    {
        $this->loadRoutesFrom(__DIR__ .'/../routes.php');
        Passport::routes();
        Route::group(['middleware' => 'oauth.providers'], function () {
            Passport::routes(function ($router) {
                return $router->forAccessTokens();
            });
        });
        $ttl=config('admin.personal_token_ttl');
        if (empty($ttl))
            $ttl= 'PT8H';
        if (file_exists(Passport::keyPath('oauth-private.key')))
            $this->app->get(AuthorizationServer::class)
                ->enableGrantType(new PersonalAccessGrant(), new \DateInterval($ttl));

        Passport::tokensExpireIn(Carbon::now()->addDays(config('admin.passport_token_ttl')));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(config('admin.passport_refresh_token_ttl')));

        if (file_exists($routes = admin_path('routes.php'))) {
            $this->loadRoutesFrom($routes);
        }
    }

    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        /*
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }*/

    }


}
