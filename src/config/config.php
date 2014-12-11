<?php

return [
    'ldap' => [
        'host' => 'ldaps://directory.srv.ualberta.ca',
        'port' => 636,
        'service_username' => getenv('AUTHENTICATION_SERVICE_USERNAME'),
        'service_password' => getenv('AUTHENTICATION_SERVICE_PASSWORD')
    ]
];
