<?php

namespace UAlberta\Authentication;

use Depotwarehouse\Toolbox\Verification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use UAlberta\Authentication\Exceptions\LDAPConnectionException;
use UAlberta\Authentication\Exceptions\LDAPSearchException;

/**
 * Class LDAPAuthenticator
 *
 * Uses LDAP to authenticate a user.
 * @package UAlberta\Authentication
 */
class LDAPAuthenticator implements AuthenticatorInterface {

    /** @var resource  */
    protected $connection;
    /** @var  UserRepositoryInterface */
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
        $this->connection = $this->initalizeLDAP();
    }

    /**
     * Initialize an LDAP connection
     * @return resource
     * @throws Exceptions\LDAPConnectionException
     */
    protected function initalizeLDAP() {
        $host = \Config::get('authentication::LDAP.host');
        $port = \Config::get('authentication::LDAP.port');
        $conn = ldap_connect($host, $port);
        if ($conn === false) {
            throw new LDAPConnectionException("Error connecting to the LDAP server {$host}:{$port}");
        }
        return $conn;
    }

    public function retrieveById($identifier)
    {
        try {
            // We first try and find the associated user record in our local table
            return $this->userRepository->findByCCID($identifier);
        } catch (ModelNotFoundException $exception) {
            // We must bind to our service account to get the employeenumber
            $service_user = \Config::get("authentication::LDAP.service_user");
            $service_password = \Config::get("authentication::LDAP.service_password");
            ldap_bind($this->connection, "uid={$service_user},ou=people,dc=ualberta,dc=ca", "{$service_password}");

            $results = ldap_search($this->connection, "ou=People,dc=ualberta,dc=ca", "(uid={$identifier})");

            if ($results === false) {
                throw new LDAPSearchException("Error searching LDAP for the required object");
            }

            $entries = ldap_get_entries($this->connection, $results);

            if ($entries === false) {
                throw new LDAPSearchException("Error searching LDAP for the required object");
            }

            if ($entries["count"] == 0) {
                throw new LDAPSearchException("Could not find any matching results in the LDAP server");
            }
            $attributes = [
                'ccid' => $entries[0]["uid"][0],
                'id' => $entries[0]["employeenumber"][0],
            ];
            return $this->userRepository->create($attributes);
        }
    }

    /**
     * @param array $credentials
     * @return bool True if credentials are correct, false otherwise
     */
    public function validateCredentials(array $credentials) {
        $requirements = [ "ccid", "password" ];
        Verification::require_set($credentials, $requirements);

        $user = $this->retrieveById($credentials["ccid"]);

        try {
            return ldap_bind($this->connection, "uid={$credentials['ccid']},ou=People,dc=ualberta,dc=ca", $credentials['password']);
        } catch (\ErrorException $exception) {
            return false;
        }
    }

} 