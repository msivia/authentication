<?php

namespace UAlberta\IST\Authentication\Exceptions;


use Exception;

class CredentialsNotFoundException extends Exception
{
    protected $case;

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
            case 1:
                $message = "{$message} Missing username for LDAP System";
                break;
            case 2:
                $message = "{$message} Missing password for LDAP System";
                break;
            case 3:
                $message = "{$message} Missing username & password for LDAP System";
                break;
            default:
                $message = "{$message} Missing credentials";
        }

        parent::__construct((string)$message);

    }
}
