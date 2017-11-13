<?php namespace Moota\SDK\Contracts\Push;

interface MatchesOrders
{
    /**
     * Matches payments sent by Moota to available orders in storage.
     * Plugin specific implementation.
     *
     * @param array $payments
     * @param array $orders
     *
     * @return array
     */
    public function match(array $payments, array $orders);
}
