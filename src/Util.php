<?php namespace Moota\SDK;

class Util
{
    public function getAuthHeader()
    {
        return $_SERVER['HTTP_AUTHORIZATION'];
    }

    public function getApiKey()
    {
        return $_GET['apikey'];
    }
}
