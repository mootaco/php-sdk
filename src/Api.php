<?php namespace Moota\SDK;

use Moota\SDK\Exceptions\MootaHttpException;
use GuzzleHttp\Client;

class Api
{
    /** @var string $baseUri */
    protected $baseUri;

    /** @var GuzzleHttp\Client $httpClient */
    protected $httpClient;

    public function __construct($apiKey = null, $timeout = null)
    {
        $apiKey = $apiKey ?: env('MOOTA_API_KEY');
        $timeout = $timeout ?: 30;

        $this->baseUri = env('SERVER_ADDR') . '/api/v1/';

        $this->httpClient = new Client([
            'base_uri' => $this->baseUri,
            'timeout'  => $timeout,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$apiKey}",
            ],
        ]);
    }

    public static function createDefault()
    {
        return new self;
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

    public function listBanks()
    {
        $queries = null;

        $responseString = $this->getEndpoint('bank', $queries);

        $banks = json_decode($responseString, true);

        return $banks;
    }
    
    /**
     * Get detailed info for a bank
     * 
     * @param string $bankId
     * @return array
     */
    public function getBank($bankId)
    {
        $bank = json_decode( $this->getEndpoint("bank/{$bankId}"), true );

        return $bank;
    }
}
