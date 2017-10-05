<?php namespace Moota\SDK\Test;

use Moota\SDK\Config;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        Config::fromArray([
            'API_KEY' => env('API_KEY'),
            'SERVER_ADDRESS' => env('SERVER_ADDRESS'),
            'SDK_MODE' => env('SDK_MODE'),
        ]);
    }
}
