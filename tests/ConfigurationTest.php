<?php

use UAlberta\IST\Authentication\Configuration;

class ConfigurationTest extends PHPUnit_Framework_TestCase {

    public function testConstructor() {
        $configuration = new Configuration();
        $this->assertAttributeInternalType("array", "ldap", $configuration);
    }

    public function testSetLdap() {
        $configuration = new Configuration();
        $keys = [
            "host" => "mock_host",
            "port" => "mock_port",
            "service_username" => "mock_username",
            "service_password" => "mock_password"
        ];
        $configuration->setLDAP($keys["host"], $keys["port"], $keys["service_username"], $keys["service_password"]);

        foreach ($keys as $key => $value) {
            $this->assertArrayHasKey($key, $configuration->ldap);
            $this->assertEquals($value, $configuration->ldap[$key]);
        }
    }
}
