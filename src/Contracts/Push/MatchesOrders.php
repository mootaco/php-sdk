<?php namespace Moota\SDK\Contracts\Push;

interface MatchesOrders
{
    /**
     * Matches payments sent by Moota to available transactions in storage.
     * Plugin specific implementation.
     *
     * @param array $payments
     * @param array $transactions
     *
     * @return array
     */
    public function match(array $payments, array $transactions);
}