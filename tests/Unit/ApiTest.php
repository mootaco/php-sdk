<?php namespace Moota\SDK\Test;

use Moota\SDK\Api;

class ApiTest extends TestCase
{
    public function testGetProfile()
    {
        $mockedApi = $this->createMock(Api::class);

        $dummy = [
            'name' => 'Dummy User',
            'email' => 'dummy@moota.com',
            'address' => null,
            'city' => null,
            'join_at' => '2017-10-02 11:53:24',
        ];

        $mockedApi->method('getEndPoint')
            ->willReturn( json_encode( $dummy ) );

        $mockedApi->method('getProfile')
            ->willReturn(
                json_decode( $mockedApi->getEndpoint('profile'), true )
            );

        $profile = $mockedApi->getProfile();

        $this->assertNotNull($profile);

        $this->assertArrayHasKey('name', $profile);
        $this->assertArrayHasKey('email', $profile);
        $this->assertArrayHasKey('address', $profile);
        $this->assertArrayHasKey('city', $profile);
        $this->assertArrayHasKey('join_at', $profile);

        $this->assertEquals($dummy['name'], $profile['name']);
        $this->assertEquals($dummy['email'], $profile['email']);
        $this->assertEquals($dummy['address'], $profile['address']);
        $this->assertEquals($dummy['city'], $profile['city']);
        $this->assertEquals($dummy['join_at'], $profile['join_at']);
    }

    public function testGetBalance()
    {
        $mockedApi = $this->createMock(Api::class);

        $dummy = [
            'balance' => mt_rand(5000, 999999),
            'bill' => mt_rand(5000, 135000),
        ];

        $mockedApi->method('getEndPoint')
            ->willReturn( json_encode( $dummy ) );

        $mockedApi->method('getBalance')
            ->willReturn(
                json_decode( $mockedApi->getEndpoint('balance'), true )
            );

        $balance = $mockedApi->getBalance();

        $this->assertNotNull($balance);

        $this->assertArrayHasKey('balance', $balance);
        $this->assertArrayHasKey('bill', $balance);

        $this->assertEquals($dummy['balance'], $balance['balance']);
        $this->assertEquals($dummy['bill'], $balance['bill']);
    }

    public function testListBanks()
    {
        $mockedApi = $this->createMock(Api::class);

        $dummy = [
            "total" => 1,
            "per_page" => 15,
            "current_page" => 1,
            "last_page" => 1,
            "next_page_url" => null,
            "prev_page_url" => null,
            "from" => 1,
            "to" => 1,
            "data" => [
              [
                "username" => "gcummings",
                "atas_nama" => "Ethyl Reichert",
                "account_number" => "1705709",
                "bank_type" => "bca",
                "is_active" => 0,
                "created_at" => "2017-10-02 11:53:24",
                "bank_id" => "l4Aqz9YzPVJ",
                "last_update" => null,
              ],
            ],
        ];

        $mockedApi->method('getEndPoint')
            ->willReturn( json_encode( $dummy ) );

        $mockedApi->method('listBanks')
            ->willReturn(
                json_decode( $mockedApi->getEndpoint('bank'), true )
            );

        $paginatedBanks = $mockedApi->listBanks();

        $this->assertNotNull($paginatedBanks);

        $this->assertArrayHasKey('total', $paginatedBanks);
        $this->assertArrayHasKey('per_page', $paginatedBanks);
        $this->assertArrayHasKey('current_page', $paginatedBanks);
        $this->assertArrayHasKey('last_page', $paginatedBanks);
        $this->assertArrayHasKey('next_page_url', $paginatedBanks);
        $this->assertArrayHasKey('prev_page_url', $paginatedBanks);
        $this->assertArrayHasKey('from', $paginatedBanks);
        $this->assertArrayHasKey('to', $paginatedBanks);
        $this->assertArrayHasKey('data', $paginatedBanks);

        $this->assertTrue(count($paginatedBanks) > 0);

        $bank = $paginatedBanks['data'][0];

        $this->assertArrayHasKey('username', $bank);
        $this->assertArrayHasKey('atas_nama', $bank);
        $this->assertArrayHasKey('account_number', $bank);
        $this->assertArrayHasKey('bank_type', $bank);
        $this->assertArrayHasKey('is_active', $bank);
        $this->assertArrayHasKey('created_at', $bank);
        $this->assertArrayHasKey('bank_id', $bank);
        $this->assertArrayHasKey('last_update', $bank);
    }

    public function testGetBank()
    {
        $mockedApi = $this->createMock(Api::class);

        $dummy = [
            'username' => 'gcummings',
            'atas_nama' => 'Ethyl Reichert',
            'account_number' => '1705709',
            'bank_type' => 'bca',
            'is_active' => 0,
            'created_at' => '2017-10-02 11:53:24',
            'bank_id' => 'l4Aqz9YzPVJ',
            'last_update' => null,
        ];

        $mockedApi->method('getEndPoint')
            ->willReturn( json_encode( $dummy ) );

        $mockedApi->method('getBank')
            ->willReturn(
                json_decode( $mockedApi->getEndpoint('bank'), true )
            );

        $bank = $mockedApi->getBank('l4Aqz9YzPVJ');

        $this->assertNotNull($bank);

        $this->assertArrayHasKey('username', $bank);
        $this->assertArrayHasKey('atas_nama', $bank);
        $this->assertArrayHasKey('account_number', $bank);
        $this->assertArrayHasKey('bank_type', $bank);
        $this->assertArrayHasKey('is_active', $bank);
        $this->assertArrayHasKey('created_at', $bank);
        $this->assertArrayHasKey('bank_id', $bank);
        $this->assertArrayHasKey('last_update', $bank);
    }

    public function testGetLastTransactions()
    {
        $mockedApi = $this->createMock(Api::class);

        $dummy = [
            [
             'date' => '2017-10-02 00:00:00',
             'description' => 'In ea enim ad beatae porro qui temporibus. Perspiciatis est expedita nisi dicta. Impedit rerum quis voluptatem accusamus sed nam quia. In nihil optio nam facere perferendis distinctio velit.',
             'amount' => 42032,
             'type' => 'DB',
             'balance' => 3922196,
             'created_at' => '2017-10-02 11:53:24',
             'mutation_id' => 'xbLjJK8WO7n',
            ]
        ];

        $mockedApi->method('getLastTransactions')
            ->willReturn($dummy);

        $transactions = $mockedApi->getLastTransactions('l4Aqz9YzPVJ');

        $this->assertNotNull($transactions);

        $this->assertTrue( count($transactions) > 0 );

        $transaction = $transactions[0];

        $this->assertArrayHasKey('date', $transaction);
        $this->assertArrayHasKey('description', $transaction);
        $this->assertArrayHasKey('amount', $transaction);
        $this->assertArrayHasKey('type', $transaction);
        $this->assertArrayHasKey('balance', $transaction);
        $this->assertArrayHasKey('created_at', $transaction);
        $this->assertArrayHasKey('mutation_id', $transaction);
    }

    public function testSearchTransactionsByAmount()
    {
        $mockedApi = $this->createMock(Api::class);

        $dummy = [
            'from' => '2017-06-05 09:41:11',
            'to' => '2017-06-12 09:41:11',
            'mutation' => [
                [
                    'date' => '2017-06-02 00:00:00',
                    'description' => 'TARIKAN ATM 02\/06',
                    'amount' => 50000,
                    'type' => 'DB',
                    'balance' => 99999999,
                    'created_at' => '2017-06-11 19:41:55',
                    'mutation_id' => 'qB',
                ]
            ]
        ];

        $mockedApi->method('searchTransactionsByAmount')
            ->willReturn($dummy);

        $searchResult = $mockedApi->searchTransactionsByAmount(
            'l4Aqz9YzPVJ', 0
        );

        $this->assertNotNull($searchResult);

        $this->assertArrayHasKey('from', $searchResult);
        $this->assertArrayHasKey('to', $searchResult);
        $this->assertArrayHasKey('mutation', $searchResult);

        $this->assertTrue( count($searchResult['mutation']) > 0 );

        $transactions = $searchResult['mutation'][0];

        $this->assertArrayHasKey('date', $transactions);
        $this->assertArrayHasKey('description', $transactions);
        $this->assertArrayHasKey('amount', $transactions);
        $this->assertArrayHasKey('type', $transactions);
        $this->assertArrayHasKey('balance', $transactions);
        $this->assertArrayHasKey('created_at', $transactions);
        $this->assertArrayHasKey('mutation_id', $transactions);
    }
}
