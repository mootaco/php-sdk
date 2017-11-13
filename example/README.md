# Panduan Penggunaan SDK
Note: saat ini hanya Push Notification Handler yang telah diimplementasikan

## Push Notification Handler

### SDK Usage

- Definisikan `class` yang akan mengambil Order / Transaction dari storage anda  
  contoh: [SampleOrderFetcher.php](./SampleOrderFetcher.php)  
  `class` ini _HARUS_ meng-`implements` interface  
  `Moota\SDK\Contracts\FetchesOrders`

- Definisikan `class` yang akan mencocokkan Order / Transaction dari storage  
  anda dengan data payment dari `Moota`  
  contoh: [SampleOrderMatcher.php](./SampleOrderMatcher.php)  
  `class` ini _HARUS_ meng-`implements` interface  
  `Moota\SDK\Contracts\MatchesOrders`

- Definisikan `class` yang akan mengeset status Order / Transaction dari storage    sebagai `complete` (selesai)  
  contoh: [SampleOrderFullfiler.php](./SampleOrderFullfiler.php)  
  `class` ini _HARUS_ meng-`implements` interface  
  `Moota\SDK\Contracts\FullfilsOrders`

- Anda tentukan parameter SDK sebagai berikut:
  ```php
    Moota\SDK\Config::fromArray(array(
        // non-null string
        // dapatkan api-key anda melalui:
        // https://app.moota.co/settings?tab=api
        'apiKey' => '',

        // non-null integer, default: 30
        // timeout, dalam detik
        'apiTimeout' => '',

        // non-null string, `production` / `testing`, default: `production`
        'env' => '',
    ));
  ```

- Handle push notification, contoh:  
  ```php
    $pushHandler = PushCallbackHandler::createDefault()
        ->setOrderFetcher(new SampleOrderFetcher)
        ->setOrderMatcher(new SampleOrderMatcher)
        ->setOrderFullfiler(new SampleOrderFullfiler)
    ;

    $response = $pushHandler->handle();
  ```

### Push Notification handling flow:
- Moota eksekusi POST ke url yang anda tentukan,  
  misalkan: `https://example.org/moota-push`  
  dengan data JSON pada body, contoh:  
  ```javascript
    [
      {
        "id": 5478,
        "bank_id": 10,
        "account_number": 96220033,
        "bank_type": "mandiri",
        "date": "10-10-2017",
        "amount": 300000,
        "description": "description",
        "type": "CR",
        "balance": 0
      },
      {
        "id": 5479,
        "bank_id": 10,
        "account_number": 96220033,
        "bank_type": "mandiri",
        "date": "10-10-2017",
        "amount": 139543,
        "description": "description",
        "type": "DB",
        "balance": 0
      }
    ]
  ```

- SDK akan men-decode data tersebut menjadi:
  ```php
    // `type` => CR: berarti transaksi uang masuk
    // `type` => DB: berarti transaksi uang keluar
    $pushData = array(
      array(
        "id" => 5478,
        "bank_id" => 10,
        "account_number" => 96220033,
        "bank_type" => "mandiri",
        "date" => "10-10-2017",
        "amount" => 300000,
        "description" => "description",
        "type" => "CR",
        "balance" => 0
      )
    );
  ```

- SDK akan mengambil Order yang tersedia pada storage / framework anda  
  sesuai dengan `amount` yang disediakan oleh `Moota` melalui data Push  
  melalui class `SampleOrderFetcher` yang telah anda sediakan  
  contoh `Order`:
  ```php
    $orders = array(
      array(
        'id' => 23,
        'total' => 150000, // seratus lima puluh ribu
        'status' => 'pending',
      ),
      array(
        'id' => 27,
        'total' => 300000, // tiga ratus ribu
        'status' => 'pending',
      ),
    );
  ```

- SDK akan mencocokkan Order yang tersedia dengan payment  
  yang disediakan oleh `Moota` melalui data Push  
  melalui class `SampleOrderMatcher` yang telah anda sediakan  
  menggunakan contoh `Order` diatas, kita dapatkan kecocokan berikut:
  ```php
    $payments = array(
      array(
          // transactionId:
          //   { orderId }-{ moota:id }-{ moota:account_number }
          'transactionId' => '27-5478-96220033',
          'orderId' => 27,
          'mootaId' => 5478,
          'mootaAccNo' => '96220033',
          'amount' => 300000,
          'mootaAmount' => 300000,
      )
    );
  ```

- SDK akan menandai Order yang cocok sebagai `complete`,  
  atau hanya menambahkan payment saja, _jika_ payment belum mencukupi  
  melalui class `SampleOrderFullfiler` yang telah anda sediakan  
