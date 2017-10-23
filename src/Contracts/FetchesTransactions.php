<?php namespace Moota\SDK\Contracts;

interface FetchesTransactions
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
