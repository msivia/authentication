<?php

namespace UAlberta\IST\Authentication\Contracts;

interface Authenticator
{

    /**
     * Validates whether or not the given credentials are valid on a particular authentication scheme.
     *
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(array $credentials);
}
