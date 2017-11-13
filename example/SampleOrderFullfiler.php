<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Moota\SDK\Contracts\FullfilsOrder;

class SampleOrderFullfiler implements FullfilsOrder
{
    public function fullfil($order)
    {
        $db->transaction();

        try {
            // Update Order Status
            $sql = "
                UPDATE `orders`
                SET `status` = 'completed'
                    , `total_paid` = `total_paid` + {$order['mootaAmount']}
                WHERE `id` = '{$order['orderId']}'
            ";

            $db->execute($sql);

            // Insert new payment for Order
            $sql = "
                INSERT INTO `order_payments` (
                    `order_id`, `amount`
                ) VALUES (
                    '{$order['orderId']}', '{$order['mootaAmount']}'
                )
            ";

            $db->execute($sql);

            // Insert new order history
            $sql = "
                INSERT INTO `order_changes` (
                    `order_id`, `description`
                ) VALUES (
                    '{$order['orderId']}'
                    , 'Moota: Payment applied, amount: {$order['mootaAmount']}'
                )
            ";

            $db->execute($sql);

            $db->commit();

            return true;
        } catch (\Exception $ex) {
            $db->rollback();

            return false;
        }
    }
}
