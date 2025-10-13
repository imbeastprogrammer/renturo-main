<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Auth\Guards\PassportTokenGuard;
use Illuminate\Support\Facades\Auth;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::loadKeysFrom('storage');
        Passport::personalAccessTokensExpireIn(now()->addMinutes(5)); // 5 minutes for testing

        // Register our custom Passport token guard
        Auth::extend('passport-token', function ($app, $name, array $config) {
            return new PassportTokenGuard(
                $app->make(\League\OAuth2\Server\ResourceServer::class),
                new \Laravel\Passport\PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
                $app->make(\Laravel\Passport\TokenRepository::class),
                $app->make(\Laravel\Passport\ClientRepository::class),
                $app->make('encrypter'),
                $app->make('request')
            );
        });
    }

    public function register()
    {
        Passport::ignoreRoutes();
    }
}
