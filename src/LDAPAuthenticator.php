<?php

namespace UAlberta\IST\Authentication;

use Depotwarehouse\Toolbox\Verification;
use Dreamscapes\Ldap\Core\LinkResource;
use Dreamscapes\Ldap\LdapException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use UAlberta\Authentication\Exceptions\LDAPConnectionException;
use UAlberta\Authentication\Exceptions\LDAPSearchException;
use UAlberta\IST\Authentication\Exceptions\ObjectNotFoundException;

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
class LDAPAuthenticator implements AuthenticatorInterface {

    /**
     * The current LDAP connection object
     * @var LinkResource
     */
    protected $connection;

    /**
     * A repository of users to serialize any data pulled from LDAP.
     * @var \UAlberta\IST\Authentication\UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Configuration of the package.
     * @var Configuration
     */
    protected $configuration;

    public function __construct(UserRepositoryInterface $userRepository, Configuration $configuration) {
        $this->userRepository = $userRepository;
        $this->configuration = $configuration;
        $this->connection = $this->initalizeLDAP();
    }

    /**
     * Initializes an LDAP Link
     * @return LinkResource
     */
    protected function initalizeLDAP() {
        $host = $this->configuration->ldap["host"];
        $port = $this->configuration->ldap["port"];
        $link = new LinkResource("{$host}:{$port}");

        return $link;
    }

    /**
     * Retrieves a User record.
     *
     * The primary source of the user record is the database, but should the record not exist in the database,
     * we will search LDAP, add it to the database, then return it.
     *
     * @param string $identifier The CCID of the user we're trying to retrieve
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    protected function retrieveById($identifier)
    {
        try {
            // We first try and find the associated user record in our local table
            return $this->userRepository->findByCCID($identifier);
        } catch (ModelNotFoundException $exception) {
            // We don't have a local copy of the user, pull it from LDAP
            $attributes = $this->retrieveFromLdap($identifier);
            return $this->userRepository->create($attributes);
        }
    }

    /**
     * @param $identifier
     * @throws ObjectNotFoundException
     * @return array
     */
    protected function retrieveFromLdap($identifier) {
        // We must bind to our service account
        $service_user = $this->configuration->ldap['service_username'];
        $service_password = $this->configuration->ldap['service_password'];
        $this->connection->bind("uid={$service_user},ou=people,dc=ualberta,dc=ca", $service_password);

        $results = $this->connection->search("ou=People,dc=ualberta,dc=ca", "(uid={$identifier})", [ "uid", "employeenumber" ], LinkResource::SCOPE_ONELEVEL);

        if ($results->countEntries() == 0) {
            throw new ObjectNotFoundException("Could not find user: {$identifier} in the LDAP system");
        }

        $entries = $results->getEntries();

        return [
            'ccid' => $entries[0]["uid"][0],
            'id' => $entries[0]["employeenumber"][0]
        ];
    }


    /**
     * Validates a user based on the given credentials.
     *
     * @param array $credentials
     * @return bool
     * @throws \Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException
     */
    public function validateCredentials(array $credentials) {
        $requirements = [ "ccid", "password" ];
        Verification::require_set($credentials, $requirements);

        $user = $this->retrieveById($credentials["ccid"]);

        try {
            return $this->connection->bind("uid={$credentials['ccid']},ou=People,dc=ualberta,dc=ca", $credentials['password']);
        } catch (LdapException $exception) {
            return false;
        }
        catch (\ErrorException $exception) {
            return false;
        }
    }

} 
