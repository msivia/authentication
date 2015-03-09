UAlberta Authentication Package
===============================

This package handles authenticating to UAlberta services. The currently supported authentication methods are:

* LDAP

Installation
-------------

Install using composer

```
composer require u-alberta/authentication
```

Laravel Integration
-------------------

Add the following line to your `config/app.php` service providers array:

```php
\UAlberta\IST\Authentication\FrameworkIntegration\Laravel\AuthenticationServiceProvider::class
```

You will also need to publish the configuration

```
php artisan vendor:publish
```

The defaults for that file should be sensible. The `AUTHENTICATION_SERVICE_USERNAME` and `AUTHENTICATION_SERVICE_PASSWORD`
environment variables need to be set in your `.env` file for the package to work properly. You can also edit the values
directly in your published configuration, but this is not recommended as it may expose passwords.

