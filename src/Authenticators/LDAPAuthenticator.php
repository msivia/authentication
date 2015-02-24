<?php

namespace UAlberta\IST\Authentication\Authenticators;

use Depotwarehouse\Toolbox\Verification;
use Depotwarehouse\Toolbox\Verification\ParameterRequiredException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use UAlberta\IST\Authentication\Configuration;
use UAlberta\IST\Authentication\Contracts\Authenticator;
use UAlberta\IST\Authentication\Contracts\UserProvider;
use UAlberta\IST\Authentication\Contracts\UserRepository;

/**
 * Class LDAPAuthenticator
 *
 * Uses LDAP to authenticate a user.
 *
 * This class contains side effects - that is authentication will also create and update users in the database.
 * This is due to the LDAP service and the extreme inefficiencies of querying and connecting to it multiple times so,
 * wherever possible, we will hit the database first and only hit the LDAP server when absolutely necessary.
 *
 * @package UAlberta\Authentication
 */
class LDAPAuthenticator implements Authenticator
{


    /**
     * A repository of users to serialize any data pulled from LDAP.
     *
     * @var \UAlberta\IST\Authentication\Contracts\UserRepository
     */
    protected $userRepository;

    /**
     * A provider of user information from a concrete source (LDAP in this case)
     *
     * @var \UAlberta\IST\Authentication\Contracts\UserProvider
     */
    protected $userProvider;

    /**
     * Configuration of the package.
     * @var Configuration
     */
    protected $configuration;

    public function __construct(
        UserRepository $userRepository,
        UserProvider $userProvider,
        Configuration $configuration
    ) {
        $this->userRepository = $userRepository;
        $this->userProvider = $userProvider;
        $this->configuration = $configuration;
    }

    /**
     * Retrieves a User record.
     *
     * The primary source of the user record is the database, but should the record not exist in the database,
     * we will search LDAP, add it to the database, then return it.
     *
     * @param string $identifier The CCID of the user we're trying to retrieve
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function retrieveById($identifier)
    {
        try {
            // We first try and find the associated user record in our local table
            return $this->userRepository->findByCCID($identifier);
        } catch (ModelNotFoundException $exception) {
            // We don't have a local copy of the user, pull it from LDAP
            $attributes = $this->userProvider->retrieveUser($identifier);
            return $this->userRepository->create($attributes);
        }
    }

    /**
     * Validates a user based on the given credentials.
     *
     * @param array $credentials
     * @return bool
     * @throws ParameterRequiredException
     */
    public function validateCredentials(array $credentials)
    {
        $requirements = [ "ccid", "password" ];
        Verification\require_set($credentials, $requirements);

        $user = $this->retrieveById($credentials["ccid"]);

        return $this->userProvider->validateUser($credentials['ccid'], $credentials['password']);

    }

} 
