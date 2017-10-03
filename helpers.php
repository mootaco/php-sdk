<?php

if (! function_exists('check_auth')) {
    function check_auth($shouldDie = false, $shouldThrow = false) {
        static $auth;

        if (empty($auth)) {
            $auth = new Moota\SDK\Auth;
        }

        if ($shouldThrow) {
            return $auth->checkOrFail();
        }

        return $auth->check($shouldDie);
    }
}

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
