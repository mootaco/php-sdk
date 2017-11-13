<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Moota\SDK\Contracts\FetchesOrders;

class SampleOrderFetcher implements FetchesOrders
{
    public function fetch(array $inflowAmounts)
    {
        $whereInflowAmounts = '';

        if (
            !empty($inflowAmounts)
            && count($inflowAmounts) > 0
            && in_array()
        ) {
            $whereInflowAmounts = implode(',', $inflowAmounts);
            $whereInflowAmounts = "WHERE `total` IN ({$whereInflowAmounts})";
            $whereInflowAmounts = "AND {$whereInflowAmounts}";
        }

        $sql = "
            SELECT `id`, `total`, `status`
            FROM `orders`
            WHERE `status` IS NOT 'complete' $whereInflowAmounts
        ";

        $orders = array();

        while ($row = $db->statement($sql)->fetchRow()) {
            $orders[] = $row;
        }

        return $orders;
    }
}
