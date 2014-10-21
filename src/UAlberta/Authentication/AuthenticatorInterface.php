<?php

namespace UAlberta\Authentication;

interface AuthenticatorInterface {

    /**
     * Validates whether or not the given credentials are valid on a particular authentication scheme.
     *
     * @param array $credentials
     * @return bool Are the credentials valid?
     */
    public function validateCredentials(array $credentials);
}