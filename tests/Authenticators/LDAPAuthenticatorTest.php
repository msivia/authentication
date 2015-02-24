<?php

use UAlberta\IST\Authentication\Authenticators\LDAPAuthenticator;
use Mockery as m;

class LDAPAuthenticatorTest extends PHPUnit_Framework_TestCase
{

    /** @var  \UAlberta\IST\Authentication\Configuration */
    protected $configuration;

    /** @var  \Mockery\MockInterface */
    protected $mock_userRepository;
    /** @var  \Mockery\MockInterface */
    protected $mock_userProvider;

    public function setUp()
    {
        $this->configuration = new \UAlberta\IST\Authentication\Configuration();
        $this->configuration->setLDAP("mock_host", "mock_port", "service_username", "service_password");
        $this->mock_userRepository = Mockery::mock('UserRepository');
        $this->mock_userProvider = Mockery::mock('\UAlberta\IST\Authentication\Providers\LDAPUserProvider');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testConstructor()
    {
        $authenticator = new LDAPAuthenticator(
            $this->mock_userRepository,
            $this->mock_userProvider,
            $this->configuration
        );
        $this->assertAttributeEquals($this->mock_userRepository, "userRepository", $authenticator);
        $this->assertAttributeEquals($this->mock_userProvider, "userProvider", $authenticator);
        $this->assertAttributeEquals($this->configuration, "configuration", $authenticator);
    }

    /**
     * @expectedException \Depotwarehouse\Toolbox\Verification\ParameterRequiredException
     * @expectedExceptionMessage A piece of data was not properly passed. Check the parameter: password
     */
    public function testCredentialsNotPassed()
    {
        $credentials = [ 'ccid' => 'mock_ccid' ];
        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider,
            $this->configuration);
        $authenticator->validateCredentials($credentials);
    }

    public function testRetrieveFromDatabase()
    {
        $credentials = [ 'ccid' => 'mock_ccid', 'password' => 'mock_password' ];
        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider,
            $this->configuration);
        $this->mock_userRepository
            ->shouldReceive('findByCCID')
            ->with('mock_ccid');
        $this->mock_userProvider->shouldReceive('validateUser')->andReturn(true);

        $result = $authenticator->validateCredentials($credentials);

        $this->assertTrue($result);
    }

    public function testRetrieveFromLDAP()
    {
        $credentials = [ 'ccid' => 'mock_ccid', 'password' => 'mock_password' ];
        $userInfo = [ 'id' => 1, 'ccid' => "mock_ccid" ];

        $authenticator = new LDAPAuthenticator($this->mock_userRepository, $this->mock_userProvider,
            $this->configuration);
        $this->mock_userRepository->shouldReceive('findByCCID')->with('mock_ccid')->andThrow('\Illuminate\Database\Eloquent\ModelNotFoundException');
        $this->mock_userProvider->shouldReceive('retrieveUser')->andReturn($userInfo);
        $this->mock_userRepository->shouldReceive('create')->with($userInfo);
        $this->mock_userProvider->shouldReceive('validateUser')->andReturn(true);

        $result = $authenticator->validateCredentials($credentials);
        $this->assertTrue($result);
    }


}

class UserRepository extends \Depotwarehouse\Toolbox\DataManagement\Repositories\ActiveRepositoryAbstract implements \UAlberta\IST\Authentication\Contracts\UserRepository
{
    public function findByCCID($ccid)
    {
    }

}
