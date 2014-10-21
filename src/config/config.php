<?php
return array(

    /**
     * The backend authentication scheme used by the system.
     * Currently available options are LDAP.
     * SAML coming soon
     */
    'backend_authentication' => 'LDAP',

    /**
     * The service user is an LDAP user that must have access to view users' employee_numbers
     */
    'LDAP' => [
        'host' => 'ldaps://directory.srv.ualberta.ca',
        'port' => 636,
        'service_user' => 'busdisa',
        'service_password' => "2CCc@c$(5&de",
    ],

    'SAML' => [

    ],

    /**
     * This property allows two factor authentication on the user object
     */
    'two_factor_enabled' => true,

    'yubikey' => [
        'client_id' => '18034',
        'apiKey' => 'i+OEHismAv+bgN5f/GtwzkcTG0Y=',
    ],
);