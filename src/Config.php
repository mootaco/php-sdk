<?php namespace Moota\SDK;

/**
 * Class Config
 *
 * A singleton in-memory config storage
 *
 * @package Moota\SDK
 */
class Config
{
    const SANDBOX_URL = 'http://moota.matamerah.com';
    const LIVE_URL = 'https://app.moota.co';

    /** @var string $apiKey */
    public static $apiKey = null;

    /** @var integer|float $apiTimeout */
    public static $apiTimeout = 30;

    /** @var string $sdkMode production or testing */
    public static $sdkMode = 'production';

    /** @var string $serverAddress */
    public static $serverAddress = 'https://app.moota.co';

    /** @var bool $useUniqueCode */
    public static $useUniqueCode = true;

    /** @var integer $uqLowerLimit */
    public static $uqLowerLimit = null;

    /** @var integer $uqUpperLimit */
    public static $uqUpperLimit = null;

    /**
     * Set Config static variable's value from an array
     *
     * @param  array Associative array of key-value pairs
     * @param  array Errors that might come up
     *
     * @return bool  Whether there are errors
     */
    public static function fromArray($values, &$errors = null)
    {
        $success = true;
        $errors = $errors ? $errors : array();

        foreach ($values as $key => $value) {
            if ($key === 'serverAddress') {
                continue;
            }

            if ( self::has($key) ) {
                self::${ $key } = $value;

                if ($key === 'sdkMode') {
                    self::$serverAddress = $value === 'production'
                        ? self::LIVE_URL : self::SANDBOX_URL;
                }
            } else {
                $errors[] = "`$key` does not exists";
                $success = $success && false;
            }
        }

        return $success;
    }

    public static function has($key)
    {
        return property_exists( self::class, $key );
    }

    public static function toArray()
    {
        return array(
            'apiKey' => self::$apiKey,
            'apiTimeout' => self::$apiTimeout,
            'sdkMode' => self::$sdkMode,
            'serverAddress' => self::$serverAddress,
            'useUniqueCode' => self::$useUniqueCode,
            'uqLowerLimit' => self::$uqLowerLimit,
            'uqUpperLimit' => self::$uqUpperLimit,
        );
    }

    public static function getServerAddress()
    {
        return self::$sdkMode === 'production'
            ? self::LIVE_URL : self::SANDBOX_URL;
    }
}
