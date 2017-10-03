<?php namespace Moota\SDK;

class PushCallbackHandler
{
    /** @var array $pushData */
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

    /** @var \Closure|null $receiverCallback */
    protected $receiverCallback;

    /**
     * @param  \Closure|null $receiverCallback
     * @return \Moota\SDK\PushCallbackHandler
     */
    public function __construct($receiverCallback = null)
    {
        $this->receiverCallback = $receiverCallback;
    }

    /**
     * Get `HTTP_RAW_DATA` in a non-deprecated way
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
        check_auth(true);

        $receiver = $this->receiverCallback
            ? $this->receiverCallback : $this->receivePushNotification;

        return json_decode( $receiver(), true );
    }
}
