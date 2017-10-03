<?php namespace Moota\SDK;

class Util
{
    public static function getAuthHeader()
    {
        return $_SERVER['HTTP_AUTHORIZATION'];
    }

    public static function getApiKey()
    {
        return $_GET['apikey'];
    }
}
