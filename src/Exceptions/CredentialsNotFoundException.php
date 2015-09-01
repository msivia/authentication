<?php

namespace UAlberta\IST\Authentication\Exceptions;


use Exception;

class CredentialsNotFoundException extends Exception
{
    protected $case;

    // Missing credentials case constants
    const USER_NOT_FOUND = 1;
    const PASSWORD_NOT_FOUND = 2;
    const USER_PASSWORD_NOT_FOUND = 3;

    /**
     * Creates a new exception.
     *
     * @param string $identifier expects CCID to be searched in LDAP
     * @param int $case
     */
    public function __construct($identifier, $case)
    {
        $this->case = $case;

        // Pass the message  to the parent
        $message = "User {$identifier} could not be looked in LDAP system:";
        switch ($this->case) {
            case $this::USER_NOT_FOUND :
                $message = "{$message} Missing username for LDAP System";
                break;
            case $this::PASSWORD_NOT_FOUND:
                $message = "{$message} Missing password for LDAP System";
                break;
            case $this::USER_PASSWORD_NOT_FOUND:
                $message = "{$message} Missing username & password for LDAP System";
                break;
            default:
                $message = "{$message} Missing credentials";
        }

        parent::__construct((string)$message);

    }
}