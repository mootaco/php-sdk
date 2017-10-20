<?php namespace Moota\SDK;

class Util
{
    public function getAuthHeader()
    {
        $authHeader = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        } else if (isset($_SERVER['AUTHORIZATION'])) {
            $authHeader = $_SERVER['AUTHORIZATION'];
        } else if (isset($_SERVER['Authorization'])) {
            $authHeader = $_SERVER['Authorization'];
        }

        return $authHeader;
    }

    public function getApiKey()
    {
        return $_GET['apikey'];
    }
}
