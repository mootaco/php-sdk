<?php namespace Moota\SDK;

use Moota\SDK\Exceptions\MootaConfigEmptyException;
use GuzzleHttp\Client;

/**
 * Class Api
 *
 * @package Moota\SDK
 */
class Api
{
    /** @var GuzzleHttp\Client $httpClient */
    protected $httpClient;

    public function __construct()
    {
        if (! Config::has('apiKey') || empty(Config::$apiKey) ) {
            throw new MootaConfigEmptyException(null, 'apiKey');
        }

        if (! Config::has('sdkMode') || empty(Config::$sdkMode) ) {
            throw new MootaConfigEmptyException(null, 'sdkMode');
        }

        if (! Config::has('apiTimeout') || empty(Config::$apiTimeout) ) {
            throw new MootaConfigEmptyException(null, 'apiTimeout');
        }

        $apiKey = Config::$apiKey;

        $this->httpClient = new Client(array(
            'base_uri' => Config::getServerAddress() . '/api/v1/',
            'timeout'  => Config::$apiTimeout,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$apiKey}",
            ],
        ));
    }

    public function getEndpoint($uri, $queries = null)
    {
        if (!empty($queries)) {
            $uri = $uri . '?' . http_build_query($queries);
        }

        $response = $this->httpClient->get($uri);

        return $response->getBody()->getContents();
    }

    /**
     * Get current user's profile
     *
     * @return array
     */
    public function getProfile()
    {
        $profile = json_decode( $this->getEndpoint('profile'), true );

        return $profile;
    }

    /**
     * Get current user's balance
     *
     * @return array
     */
    public function getBalance()
    {
        $balance = json_decode( $this->getEndpoint('balance'), true );

        return $balance;
    }

    /**
     * Get all banks registered to current Moota customer
     * @param int $page
     *
     * @return array
     */
    public function listBanks($page = null)
    {
        $queries = empty($page) ? array() : compact('page');

        $responseString = $this->getEndpoint('bank', $queries);

        $banks = json_decode($responseString, true);

        return $banks;
    }

    /**
     * Get detailed info for a bank
     *
     * @param string $bankId
     *
     * @return array
     */
    public function getBank($bankId)
    {
        $bank = json_decode( $this->getEndpoint("bank/{$bankId}"), true );

        return $bank;
    }

    /**
     * @param integer $bankId
     * @param integer $transactionCount
     *
     * @return array
     */
    public function getLastTransactions($bankId, $transactionCount = null)
    {
        $transactionCount = $transactionCount ?: 5;

        $transactions = json_decode($this->getEndpoint(
            "bank/{$bankId}/mutation/recent/{$transactionCount}"
        ), true);

        return $transactions;
    }

    /**
     * @param integer $bankId
     * @param integer $amount
     *
     * @return array
     */
    public function searchTransactionsByAmount($bankId, $amount)
    {
        $transactions = json_decode($this->getEndpoint(
            "bank/{$bankId}/mutation/search/{$amount}"
        ), true);

        return $transactions;
    }

    /**
     * @param integer $bankId
     * @param string $desc
     *
     * @return array
     */
    public function searchTransactionsByDesc($bankId, $desc)
    {
        $transactions = json_decode($this->getEndpoint(
            "bank/{$bankId}/mutation/search/description/{$desc}"
        ), true);

        return $transactions;
    }

    public function linkOrderWithMoota($mootaId, $orderId)
    {
        // TODO: Implement #linkOrderWithMoota
    }
}
