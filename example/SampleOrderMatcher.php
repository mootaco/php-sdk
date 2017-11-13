<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Moota\SDK\Contracts\MatchesOrders;

class SampleOrderMatcher implements MatchesOrders
{
    public function match(array $payments, array $orders)
    {
        $matchedPayments = [];

        // array assignment di php _SELALU_ copy by values
        // untuk array 1 dimensi, ini sama dengan clone
        $guardedPayments = $payments;

        if ( ! empty($orders) && count($orders) > 0 ) {
            // match your orders with moota payments
            // TODO: apply unique code transformation over here
            foreach ($orders as $order) {
                $transAmount = (float) $order['total'];
                $tmpPayment = null;

                foreach ($guardedPayments as $i => $mootaInflow) {
                    // payment yang ini sudah ada kecocokan, lewati saja
                    if (empty($guardedPayments[ $i ])) {
                        continue;
                    }

                    // amount dari moota, selalu ber-tipe float
                    // sesuaikan dengan amount pada sistem anda
                    if ( ( (float) $mootaInflow['amount'] ) === $transAmount ) {
                        $tmpPayment = $mootaInflow;

                        // payment yang ini sudah ada kecocokan, kosongkan saja
                        // sehingga untuk order selanjutnya akan dilewati
                        $guardedPayments[ $i ] = null;

                        break;
                    }
                }

                if (!empty($tmpPayment)) {
                    $matchedPayments[]  = array(
                        // transactionId:
                        //   { orderId }-{ moota:id }-{ moota:account_number }
                        'transactionId' => implode('-', [
                            $order->id,
                            $tmpPayment['id'],
                            $tmpPayment['account_number']
                        ]),

                        'orderId' => $order->id,
                        'mootaId' => $tmpPayment['id'],
                        'mootaAccNo' => $tmpPayment['account_number'],
                        'amount' => $tmpPayment['amount'],
                        'mootaAmount' => $tmpPayment['amount'],
                    );
                }
            }
        }

        return $matchedPayments;
    }
}
