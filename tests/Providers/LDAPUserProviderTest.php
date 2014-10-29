<?php

use UAlberta\IST\Authentication\Providers\LDAPUserProvider;

class LDAPUserProviderTest extends PHPUnit_Framework_TestCase {

    /** @var  \Mockery\MockInterface */
    protected $mock_ldap;

    /** @var  \UAlberta\IST\Authentication\Configuration */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new \UAlberta\IST\Authentication\Configuration();
        $this->configuration->setLDAP("mock_host", "mock_port", "service_username", "service_password");
        $this->mock_ldap = Mockery::mock('\Dreamscapes\Ldap\Core\LinkResource');
    }

    public function tearDown() {
        Mockery::close();
    }

    public function testConstructor() {
        $userProvider = new LDAPUserProvider($this->mock_ldap, $this->configuration);
        $this->assertAttributeEquals($this->mock_ldap, "connection", $userProvider);
        $this->assertAttributeEquals($this->configuration, "configuration", $userProvider);
    }

    public function testValidateUserTrue() {
        $userProvider = new LDAPUserProvider($this->mock_ldap, $this->configuration);
        $this->mock_ldap->shouldReceive('bind');
        $result = $userProvider->validateUser("mock_ccid", "mock_password");
        $this->assertTrue($result);
    }

    public function testValidateUserFalse() {
        $userProvider = new LDAPUserProvider($this->mock_ldap, $this->configuration);
        $this->mock_ldap->shouldReceive('bind')->andThrow('Exception');
        $result = $userProvider->validateUser("mock_ccid", "mock_password");

        $this->assertFalse($result);
    }

}
