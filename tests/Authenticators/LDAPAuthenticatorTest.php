<?php

use Depotwarehouse\Toolbox\DataManagement\EloquentModels\BaseModel;
use UAlberta\IST\Authentication\Authenticators\LDAPAuthenticator;

class LDAPAuthenticatorTest extends PHPUnit_Framework_TestCase {

    /** @var  \UAlberta\IST\Authentication\Configuration */
    protected $configuration;

    /** @var  \Mockery\MockInterface */
    protected $mock_userRepository;
    /** @var  \Mockery\MockInterface */
    protected $mock_userProvider;

    public function setUp() {
        $this->configuration = new \UAlberta\IST\Authentication\Configuration();
        $this->configuration->setLDAP("mock_host", "mock_port", "service_username", "service_password");
        $this->mock_userRepository = Mockery::mock('UserRepository');
        $this->mock_userProvider = Mockery::mock('\UAlberta\IST\Authentication\Providers\LDAPUserProvider');
    }

    public function tearDown() {
        Mockery::close();
    }

    public function testConstructor() {
        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider, $this->configuration);
        $this->assertAttributeEquals($this->mock_userRepository, "userRepository", $authenticator);
        $this->assertAttributeEquals($this->mock_userProvider, "userProvider", $authenticator);
        $this->assertAttributeEquals($this->configuration, "configuration", $authenticator);
    }

    /**
     * @expectedException \Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException
     * @expectedExceptionMessage A piece of data was not properly passed. Check the parameter: password
     */
    public function testCredentialsNotPassed() {
        $credentials = [ 'ccid' => 'mock_ccid' ];
        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider, $this->configuration);
        $authenticator->validateCredentials($credentials);
    }

    public function testRetrieveFromDatabase() {
        $credentials = [ 'ccid' => 'mock_ccid', 'password' => 'mock_password' ];
        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider, $this->configuration);
        $this->mock_userRepository->shouldReceive('findByCCID')->with('mock_ccid')->andReturn(new BaseModel());
        $this->mock_userProvider->shouldReceive('validateUser')->andReturn(true);

        $result = $authenticator->validateCredentials($credentials);

        $this->assertTrue($result);
    }

    public function testRetrieveFromLDAP() {
        $credentials = [ 'ccid' => 'mock_ccid', 'password' => 'mock_password' ];
        $userInfo = [ 'id' => 1, 'ccid' => "mock_ccid" ];

        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider, $this->configuration);
        $this->mock_userRepository->shouldReceive('findByCCID')->with('mock_ccid')->andThrow('\Illuminate\Database\Eloquent\ModelNotFoundException');
        $this->mock_userProvider->shouldReceive('retrieveUser')->andReturn($userInfo);
        $this->mock_userRepository->shouldReceive('create')->with($userInfo);
        $this->mock_userProvider->shouldReceive('validateUser')->andReturn(true);

        $result = $authenticator->validateCredentials($credentials);
        $this->assertTrue($result);
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
