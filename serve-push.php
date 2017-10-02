<?php

/**
 * MootaSDK - A PHP SDK for https://moota.co
 *
 * @package  MootaSDK
 * @author   Aprilus Lumbantoruan <i@pilus.me>
 */

require_once __DIR__ . '/vendor/autoload.php';

$pushData = \Moota\SDK\PushCallbackHandler::decode();
