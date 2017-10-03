<?php

require_once __DIR__ . '/vendor/autoload.php';

if (env('MOOTA_MODE') !== 'testing') {
    Kint::$enabled_mode = false;
}
