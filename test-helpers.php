<?php

if (! function_exists('intercept_auth')) {
    function intercept_auth() {
        static $hasBeenForced;

        if ($hasBeenForced === true) {
            return;
        }

        if (empty($hasBeenForced)) {
            $hasBeenForced = false;
        }

        // intercept calls to `check` method on `Auth` class
        // then return true all the time, only for testing
        uopz_set_return(Moota\SDK\Auth::class, 'check', true);
        $hasBeenForced = true;
    }
}
