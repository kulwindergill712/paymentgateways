<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // app(AuthorizationServer::class)->enableGrantType(
        //     $this->AppleLoginGrant(), Passport::tokensExpireIn()
        // );

        $this->registerPolicies();

        //
    }
    protected function AppleLoginGrant()
    {
        // $grant = new PhoneLoginController(
        //     $this->app->make(RefreshTokenRepository::class)
        // );
        // $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        // return $grant;
    }
}
