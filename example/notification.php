<?php

die('Example only, cannot be executed');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/SampleOrderFetcher.php';
require_once __DIR__ . '/SampleOrderMatcher.php';
require_once __DIR__ . '/SampleOrderFullfiler.php';

Moota\SDK\Config::fromArray(array(
    'apiKey' => $config[ MOOTA_API_KEY ],
    'apiTimeout' => $config[ MOOTA_API_TIMEOUT ],
    'env' => strtolower( $config[ MOOTA_ENV ] ),
));

$pushHandler = PushCallbackHandler::createDefault()
    ->setOrderFetcher(new SampleOrderFetcher)
    ->setOrderMatcher(new SampleOrderMatcher)
    ->setOrderFullfiler(new SampleOrderFullfiler)
;

// Jika ada order yang telah berhasil diupdate, isi `$response` adalah:
//    array(
//        'status' => 'ok', 'count' => (non-null-integer)
//    );
// jika tidak:
//    array(
//        'status' => 'not-ok', 'message' => 'No matching order found'
//    );
$response = $pushHandler->handle();
