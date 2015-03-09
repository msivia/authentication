<?php

namespace UAlberta\IST\Authentication\FrameworkIntegration\Laravel;

use Config;
use Dreamscapes\Ldap\Core\LinkResource;
use Illuminate\Support\ServiceProvider;
use UAlberta\IST\Authentication\Authenticators\LDAPAuthenticator;
use UAlberta\IST\Authentication\Configuration;
use UAlberta\IST\Authentication\Contracts\Authenticator;
use UAlberta\IST\Authentication\Contracts\UserProvider;
use UAlberta\IST\Authentication\Providers\LDAPUserProvider;

class AuthenticationServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $ldap_config = Config::get('authentication.ldap');

        $this->app->bind(Configuration::class, function () use ($ldap_config) {
            $configuration = new Configuration();
            $configuration->setLDAP(
                $ldap_config['host'],
                $ldap_config['port'],
                $ldap_config['service_username'],
                $ldap_config['service_password']
            );

            return $configuration;
        });

        $this->app->bind(UserProvider::class, LDAPUserProvider::class);
        $this->app->bind(Authenticator::class, LDAPAuthenticator::class);

        $this->app->bind(LinkResource::class, function () use ($ldap_config) {
            $resource = new LinkResource("{$ldap_config['host']}:{$ldap_config['port']}");
            return $resource;
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/authentication.php' => config_path('authentication.php')
        ]);
    }

}
