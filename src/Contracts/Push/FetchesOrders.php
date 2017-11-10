<?php namespace Moota\SDK\Contracts\Push;

interface FetchesOrders
{
    /**
     * Fetches currently available transaction in storage.
     * Plugin specific implementation.
     *
     * @param array $inflowAmounts
     *
     * @return array
     */
    public function fetch(array $inflowAmounts);
}
