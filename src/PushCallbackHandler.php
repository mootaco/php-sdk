<?php namespace Moota\SDK;

class PushCallbackHandler
{
    protected $pushData = [
        'id' => null,
        'bank_id' => null,
        'account_number' => null,
        'bank_type' => null,
        'date' => null,
        'amount' => null,
        'description' => null,
        'type' => null,
        'balance' => null,
    ];

    protected $receiverCallback;

    public function __construct($receiverCallback = null)
    {
        $this->receiverCallback = $receiverCallback;
    }

    /**
     * Get `HTTP_RAW_DATA` in a modern way
     *
     * Returns a json string
     *
     * @return string
     */
    protected function receivePushNotification() {
        return file_get_contents('php://input');
    }

    /**
     * Handles Moota's Push Notification Post request
     * 
     * Returns an array of associated array, e.g.:
     * 
     * ```
     * [
     *     [
     *         'id' => null,
     *         'bank_id' => null,
     *         'account_number' => null,
     *         'bank_type' => null,
     *         'date' => null,
     *         'amount' => null,
     *         'description' => null,
     *         'type' => null,
     *         'balance' => null,
     *     ]
     * ]
     * ```
     * 
     * @return array
     */
    public function decode()
    {
        $receiver = $this->receiverCallback
            ? $this->receiverCallback : $this->receivePushNotification;

        $pushData = json_decode( $receiver(), true );

        return $pushData === false ? self::$pushData : $pushData;
    }
}
