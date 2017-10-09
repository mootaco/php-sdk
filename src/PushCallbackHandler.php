<?php namespace Moota\SDK;

class PushCallbackHandler
{
    /** @var Auth $authChecker */
    protected $authChecker;

    /** @var \Closure|null $receiverCallback */
    protected $receiverCallback;

    /**
     * @param  \Closure|null $receiverCallback
     * @return \Moota\SDK\PushCallbackHandler
     */
    public function __construct(Auth $authChecker, $receiverCallback = null)
    {
        $this->authChecker = $authChecker;
        $this->receiverCallback = $receiverCallback;
    }

    public static function createDefault()
    {
        return new self(Auth::createDefault());
    }

    /**
     * Get `HTTP_RAW_DATA` in a non-deprecated way
     *
     * Returns a json string
     *
     * @return string
     */
    protected function receivePushNotification()
    {
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
        $this->authChecker->check(true);

        $strPushData = null;

        if (!empty($this->receiverCallback)) {
            $strPushData = $this->receiverCallback();
        } else {
            $strPushData = $this->receivePushNotification();
        }

        return json_decode( $strPushData, true );
    }
}
