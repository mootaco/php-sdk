<?php

if (! function_exists('env')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return $default instanceof Closure ? $default() : $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (
            strlen($value) > 1
            && $value[0] === '"'
            && $value[ strlen($value) - 1 ] === '"'
        ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}

if (! function_exists('random_string')) {
    /**
     * Gets the value of an environment variable.
     *
     * @param  integer  $length
     * @return string
     */
    function random_string($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charsLen = strlen($characters);

        $randString = '';

        for ($i = 0; $i < $length; $i++) {
            $randString .= $characters[ rand( 0, $charsLen - 1 ) ];
        }

        return $randString;
    }
}

if (! function_exists('camel_case')) {
    function camel_case($value)
    {
        $value = strtolower($value);
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', lcfirst($value));
    }
}
