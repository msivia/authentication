<?php

class LDAPAuthenticatorTest extends PHPUnit_Framework_TestCase {

    /** @var  \UAlberta\IST\Authentication\Configuration */
    protected $configuration;
    /** @var  \Dreamscapes\Ldap\Core\LinkResource */
    protected $mock_ldap;

    protected $mock_userRepository;

    public function setUp() {
        $this->configuration = new \UAlberta\IST\Authentication\Configuration();
        $this->configuration->setLDAP("mock_host", "mock_port", "service_username", "service_password");
        $this->mock_ldap = Mockery::mock('\Dreamscapes\Ldap\Core\LinkResource');
        $this->mock_userRepository = Mockery::mock('UserRepository');
    }

    public function testConstructor() {
        $authenticator = new \UAlberta\IST\Authentication\LDAPAuthenticator($this->mock_userRepository, $this->configuration, $this->mock_ldap);
        $this->assertAttributeEquals($this->mock_userRepository, "userRepository", $authenticator);
        $this->assertAttributeEquals($this->configuration, "configuration", $authenticator);
        $this->assertAttributeEquals($this->mock_ldap, "connection", $authenticator);
    }


}

class UserRepository extends \Depotwarehouse\Toolbox\DataManagement\Repositories\BaseRepositoryAbstract implements \UAlberta\IST\Authentication\UserRepositoryInterface {
    /**
     * Resolves the configuration object of the class.
     *
     * In order to decouple from frameworks, configuration of this class is done through a Configuration object.
     * However, since this class is meant to be overridden, putting Configuration instantiation in the constructor
     * would require significant boilerplate on the part of the user in order to instantiate and explicitly call
     * constructors with a Configuration object.
     *
     * Rather, the user must implement the method to resolve configuration. This method has a single function
     * which is to simply set $this->getConfiguration() to a Configuration object acceptable to the client.
     *
     * It is recommended that each project implement resolveConfiguration in a single BaseRepository, then have
     * all your repositories extend from that, however you are welcome to implement the function on a per-repository
     * basis
     *
     * @return void
     */
    function resolveConfiguration()
    {}

    /**
     * Finds a user by their CCID
     * @param $ccid
     * @return \Depotwarehouse\Toolbox\Datamanagement\EloquentModels\BaseModel|static
     */
    public function findByCCID($ccid)
    {}

}
