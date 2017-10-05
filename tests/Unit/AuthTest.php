<?php namespace Moota\SDK\Test;

use Moota\SDK\Auth;
use Moota\SDK\Config;

class AuthTest extends TestCase
{
    protected function getMockedUtil(
        $mode = 'testing', $token = null, $header = null
    )
    {
        Config::$sdkMode = $mode;

        $mock = $this->createMock(\Moota\SDK\Util::class);

        if (!empty($token)) {
            Config::$apiKey = $token;

            $mock->method('getApiKey')
                ->willReturn($token);
        }

        if (!empty($header)) {
            $mock->method('getAuthHeader')
                ->willReturn("Basic {$header}");
        }

        return $mock;
    }

    public function testCheck()
    {
        $token = random_string(50);
        $header = $token . ':' . time();

        $mockedUtil = $this->getMockedUtil('production', $token, $header);

        $this->assertTrue((new Auth($mockedUtil))->check());
    }

    public function testCheckHasHeaderAndProductionSuccess()
    {
        $token = random_string(50);
        $header = $token . ':' . time();

        $mockedUtil = $this->getMockedUtil('production', $token, $header);

        $this->assertTrue((new Auth($mockedUtil))->check());
    }

    public function testCheckHasHeaderAndTestingSuccess()
    {
        $token = random_string(50);
        $header = $token . ':' . time();

        $mockedUtil = $this->getMockedUtil('testing', $token, $header);

        $this->assertTrue((new Auth($mockedUtil))->check());
    }

    public function testCheckNoHeaderAndProductionSuccess()
    {
        $token = random_string(50);
        $header = null;

        $mockedUtil = $this->getMockedUtil('production', $token, $header);

        $this->assertTrue((new Auth($mockedUtil))->check());
    }

    public function testCheckNoHeaderAndTestingSuccess()
    {
        $token = 'testing';
        $header = null;

        $mockedUtil = $this->getMockedUtil('testing', $token, $header);

        $this->assertTrue((new Auth($mockedUtil))->check());
    }

    public function testCheckMustFail()
    {
        $token = null;
        $header = null;

        $mockedUtil = $this->getMockedUtil('production', $token, $header);

        $this->assertFalse((new Auth($mockedUtil))->check());
    }
}
