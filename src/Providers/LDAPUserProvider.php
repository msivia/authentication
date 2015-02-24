<?php

namespace UAlberta\IST\Authentication\Providers;

use Dreamscapes\Ldap\Core\LinkResource;
use UAlberta\IST\Authentication\Configuration;
use UAlberta\IST\Authentication\Contracts\UserProvider;
use UAlberta\IST\Authentication\Exceptions\ObjectNotFoundException;

class LDAPUserProvider implements UserProvider {

    /**
     * The current LDAP connection object
     * @var LinkResource
     */
    protected $connection;

    /**
     * Configuration of the package.
     * @var Configuration
     */
    protected $configuration;

    public function __construct(LinkResource $linkResource, Configuration $configuration) {
        $this->connection = $linkResource;
        $this->configuration = $configuration;
    }

    /**
     * Retrieves a user from a concrete source.
     *
     * The return of the user takes the form of an associative array of the attributes of that user.
     *
     * @param $identifier
     * @throws ObjectNotFoundException
     * @return string[]
     */
    public function retrieveUser($identifier)
    {
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
     * Validates that the user's credentials are correct.
     *
     * @param $identifier
     * @param $password
     * @return bool
     */
    public function validateUser($identifier, $password)
    {
        try {
            $this->connection->bind("uid={$identifier},ou=People,dc=ualberta,dc=ca", $password);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
