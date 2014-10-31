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

Add the following line to your `app/config/app.php` service providers array:

```
'\UAlberta\IST\Authentication\FrameworkIntegration\Laravel\AuthenticationServiceProvider'
```

You will also need to publish the configuration

```
php artisan config:publish u-alberta/authentication
```

The defaults for that file should be sensible, but you will also need to populate environment variables with the following:

// TODO table of environment variables.

