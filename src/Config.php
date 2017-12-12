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

    /** @var bool $useUniqueCode */
    public static $useUniqueCode = true;

    /** @var string $uqMode INCREASE or DECREASE */
    public static $uqMode = null;

    /** @var integer $uqMin */
    public static $uqMin = null;

    /** @var integer $uqMax */
    public static $uqMax = null;

    /**
     * Set Config static variable's value from an array
     *
     * @param array $values Associative array of key-value pairs
     * @param array $errors Errors that might come up
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
            } else {
                $errors[] = "`$key` does not exists";
                $success = $success && false;
            }
        }

        return $success;
    }

    public static function has($key)
    {
        return property_exists(self::class, $key);
    }

    public static function toArray()
    {
        return array(
            'apiKey' => self::$apiKey,
            'apiTimeout' => self::$apiTimeout,
            'sdkMode' => self::$sdkMode,
            'useUniqueCode' => self::$useUniqueCode,
            'uqMode' => self::$uqMode,
            'uqMin' => self::$uqMin,
            'uqMax' => self::$uqMax,
        );
    }

    public static function getServerAddress()
    {
        return self::isLive() ? self::LIVE_URL : self::SANDBOX_URL;
    }

    public static function isLive()
    {
        return self::$sdkMode === 'production';
    }

    public static function isProduction()
    {
        return self::isLive();
    }

    public static function isTesting()
    {
        return ! self::isLive();
    }

    public static function isSandbox()
    {
        return ! self::isLive();
    }
}
