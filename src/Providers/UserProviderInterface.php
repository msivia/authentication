<?php

namespace UAlberta\IST\Authentication\Providers;

interface UserProviderInterface {

    /**
     * Retrieves a user from a concrete source.
     *
     * The return of the user takes the form of an associative array of the attributes of that user.
     *
     * @param $identifier
     * @return string[]
     */
    public function retrieveUser($identifier);

    /**
     * Validates that the user's credentials are correct.
     *
     * @param $identifier
     * @param $password
     * @return bool
     */
    public function validateUser($identifier, $password);

}
