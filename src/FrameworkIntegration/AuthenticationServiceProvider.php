<?php

namespace UAlberta\IST\Authentication\FrameworkIntegration;

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
        $this->package('u-alberta/authentication');
        $this->app->bind('\UAlberta\IST\Authentication\Configuration', function() {
            $configuration = new Configuration();
            $configuration->setLDAP(
                \Config::get('authentication::ldap.host'),
                \Config::get('authentication::ldap.port'),
                \Config::get('authentication::ldap.service_username'),
                \Config::get('authentication::ldap.service_port')
            );

            return $configuration;
        });

        $this->app->bind(
            '\UAlberta\IST\Authentication\Providers\UserProviderInterface',
            '\UAlberta\IST\Authentication\Providers\LDAPUserProvider'
        );

        $this->app->bind('\Dreamscapes\Ldap\Core\LinkResource', function() {
            $resource = new LinkResource(\Config::get('authentication::ldap.host') . ":" . \Config::get('authentication::ldap.port'));
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
