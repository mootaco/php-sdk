<?php

use Moota\SDK\PushCallbackHandler;
use PHPUnit\Framework\TestCase;

class PushCallbackHandlerTest extends TestCase
{
    public function testDecode()
    {
        $banks = ['bca', 'mandiri', 'bri'];

        $transTypes = ['CR', 'DB'];

        $dummyData = [[
            'id' => mt_rand(200, 7000),
            'bank_id' => mt_rand(1, 10),
            'account_number' => mt_rand(91050075, 99999999),
            'bank_type' => $banks[ mt_rand(0, 2) ],
            'date' => date( 'm-d-Y', mt_rand( time() - 604800, time() ) ),
            'amount' => mt_rand( 0, 500000 ),
            'description' => 'description',
            'type' => $transTypes[ mt_rand( 0, 1 ) ],
            'balance' => 0,
        ]];

        $pushHandler = new PushCallbackHandler(function () use ($dummyData) {
            return json_encode($dummyData);
        });

        $pushData = $pushHandler->decode();

        $this->assertEquals(1, count($pushData));

        $pushData = $pushData[0];
        $dummyData = $dummyData[0];

        $this->assertEquals($pushData['id'], $dummyData['id']);

        $this->assertEquals($pushData['bank_id'], $dummyData['bank_id']);

        $this->assertEquals(
            $pushData['account_number'], $dummyData['account_number']
        );

        $this->assertEquals($pushData['bank_type'], $dummyData['bank_type']);

        $this->assertEquals($pushData['date'], $dummyData['date']);

        $this->assertEquals($pushData['amount'], $dummyData['amount']);

        $this->assertEquals(
            $pushData['description'], $dummyData['description']
        );

        $this->assertEquals($pushData['type'], $dummyData['type']);

        $this->assertEquals($pushData['balance'], $dummyData['balance']);
    }
}
