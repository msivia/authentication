<?php

namespace UAlberta\IST\Authentication\Laravel\FrameworkIntegration;

use Dreamscapes\Ldap\Core\LinkResource;
use Illuminate\Support\ServiceProvider;
use UAlberta\IST\Authentication\Configuration;

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
        $this->app->bind('\UAlberta\IST\Authentication\Configuration', function() use ($ldap_config) {
            $configuration = new Configuration();
            $configuration->setLDAP(
                $ldap_config['host'],
                $ldap_config['port'],
                $ldap_config['service_username'],
                $ldap_config['service_password']
            );

            return $configuration;
        });

        $this->app->bind(
            '\UAlberta\IST\Authentication\Providers\UserProviderInterface',
            '\UAlberta\IST\Authentication\Providers\LDAPUserProvider'
        );

        $this->app->bind('\Dreamscapes\Ldap\Core\LinkResource', function() use ($ldap_config) {
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
