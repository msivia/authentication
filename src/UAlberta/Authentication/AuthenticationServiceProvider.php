<?php namespace UAlberta\Authentication;

use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton('UAlberta\Authentication\UserRepository', function() {
            return new UserRepository(new User);
        });

        switch (\Config::get('authentication::backend_authentication')) {
            case "LDAP":
                break;
            case "SAML":
                break;
        }
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
