<?php
namespace UAlberta\IST\Authentication\Exceptions;

use Exception;

class ObjectNotFoundException extends Exception
{
    /**
     * Creates a new exception.
     *
     * @param string $identifier expects CCID for approved user
     */
    public function __construct($identifier)
    {
        // Pass the message and integer code to the parent
        $message = "Could not find user: {$identifier} in the LDAP system";
        parent::__construct((string)$message);

    }
}
