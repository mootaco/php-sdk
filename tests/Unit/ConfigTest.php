<?php namespace Moota\SDK\Test;

use Moota\SDK\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testFromArray()
    {
        $key = 'API_KEY';
        $camelKey = camel_case($key);

        $failedKey = 'NON_EXISTANT';
        $failedCamelKey = camel_case($failedKey);

        Config::fromArray([
            $key => 'key',
            $failedKey => 'key',
        ]);

        $this->assertEquals( $camelKey, 'apiKey' );

        $this->assertNotNull(Config::$apiKey);

        $this->assertTrue( Config::has( $camelKey ) );
    }
}
