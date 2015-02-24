<?php

namespace UAlberta\IST\Authentication;

/**
 * Class Configuration.
 *
 * This class manages the configuration of the application to decouple it from any framework that may be calling it.
 * For specific laravel integrations, this will be bound to the config file, see AuthenticationServiceProvider
 *
 * @package UAlberta\IST\Authentication
 */
class Configuration
{

    /**
     * Configuration related to LDAP.
     *
     * Available keys are:
     *   - host
     *   - port
     *   - service_username
     *   - service_password
     * @var string[]
     */
    public $ldap;

    public function __construct()
    {
        $this->ldap = [ ];
    }

    public function setLDAP($host, $port, $service_username, $service_password)
    {
        $this->ldap["host"] = $host;
        $this->ldap["port"] = $port;
        $this->ldap["service_username"] = $service_username;
        $this->ldap["service_password"] = $service_password;
    }

} 
