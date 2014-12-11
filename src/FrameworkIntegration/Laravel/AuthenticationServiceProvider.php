<?php

namespace UAlberta\IST\Authentication\FrameworkIntegration\Laravel;

use Dreamscapes\Ldap\Core\LinkResource;
use Illuminate\Support\ServiceProvider;
use UAlberta\IST\Authentication\Authenticators\AuthenticatorInterface;
use UAlberta\IST\Authentication\Authenticators\LDAPAuthenticator;
use UAlberta\IST\Authentication\Configuration;
use UAlberta\IST\Authentication\Providers\LDAPUserProvider;
use UAlberta\IST\Authentication\Providers\UserProviderInterface;

class AuthenticationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

    public function boot() {
        // Packages
        $this->package('u-alberta/authentication', null, __DIR__);
        $ldap_config = \Config::get('authentication::ldap');
        $this->app->bind(Configuration::class, function() use ($ldap_config) {
            $configuration = new Configuration();
            $configuration->setLDAP(
                $ldap_config['host'],
                $ldap_config['port'],
                $ldap_config['service_username'],
                $ldap_config['service_password']
            );

            return $configuration;
        });

        $this->app->bind(UserProviderInterface::class, LDAPUserProvider::class);
        $this->app->bind(AuthenticatorInterface::class, LDAPAuthenticator::class);

        $this->app->bind(LinkResource::class, function() use ($ldap_config) {
            $resource = new LinkResource("{$ldap_config['host']}:{$ldap_config['port']}");
            return $resource;
        });
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
