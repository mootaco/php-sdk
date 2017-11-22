<?php  namespace Moota\SDK\Test;

use Moota\SDK\Auth;
use Moota\SDK\Contracts\Push\FetchesOrders;
use Moota\SDK\Contracts\Push\MatchesOrders;
use Moota\SDK\Contracts\Push\FulfillsOrder;
use Moota\SDK\PushCallbackHandler;

class PushCallbackHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $banks = array('bca', 'mandiri', 'bri');

        $transTypes = array('CR', 'DB');

        $dummyData = array( array(
            'id' => mt_rand(200, 7000),
            'bank_id' => mt_rand(1, 10),
            'account_number' => mt_rand(91050075, 99999999),
            'bank_type' => $banks[ mt_rand(0, 2) ],
            'date' => date( 'm-d-Y', mt_rand( time() - 604800, time() ) ),
            'amount' => mt_rand( 0, 500000 ),
            'description' => 'description',
            'type' => $transTypes[ mt_rand( 0, 1 ) ],
            'balance' => 0,
        ) );

        $mockedAuthChecker = $this->createMock(Auth::class);
        $mockedAuthChecker->method('check')->willReturn(true);

        $mockedOrderFetcher = $this->createMock(FetchesOrders::class);
        $mockedOrderFetcher->method('fetch')->willReturn($dummyData);

        $mockedOrderMatcher = $this->createMock(MatchesOrders::class);
        $mockedOrderMatcher->method('match')->willReturn($dummyData);

        $mockedOrderFullfiler = $this->createMock(FulfillsOrder::class);
        $mockedOrderFullfiler->method('fullfil')->willReturn(true);

        $pushHandler = (new PushCallbackHandler(
            $mockedAuthChecker,
            function () use ($dummyData) {
                return json_encode($dummyData);
            }
        ))
            ->setOrderFetcher($mockedOrderFetcher)
            ->setOrderMatcher($mockedOrderMatcher)
            ->setOrderFulfiller($mockedOrderFullfiler)
        ;

        $response = $pushHandler->handle();

        $this->assertNotNull($response);

        // using assertContains here somehow fails on php7
        $this->assertTrue( array_key_exists('status', $response) );

        $this->assertEquals($response['status'], 'ok');

        $mockedOrderFullfiler = $this->createMock(FulfillsOrder::class);
        $mockedOrderFullfiler->method('fullfil')->willReturn(false);

        $pushHandler->setOrderFulfiller($mockedOrderFullfiler);

        $response = $pushHandler->handle();

        $this->assertNotNull($response);

        // using assertContains here somehow fails on php7
        $this->assertTrue( array_key_exists('status', $response) );

        $this->assertEquals($response['status'], 'not-found');
    }
}
