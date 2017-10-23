<?php namespace Moota\SDK;

use Moota\SDK\Contracts\FetchesTransactions;
use Moota\SDK\Contracts\MatchPayments;

class PushCallbackHandler
{
    /** @var Auth $authChecker */
    protected $authChecker;

    /** @var \Closure|null $receiverCallback */
    protected $receiverCallback;

    /** @var FetchesTransactions $transFetcher */
    protected $transFetcher;

    /** @var MatchPayments $paymentsMatcher */
    protected $paymentsMatcher;

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

    public function setTransactionFetcher(FetchesTransactions $fetcher)
    {
        $this->transFetcher = $fetcher;

        return $this;
    }

    public function setPaymentMatcher(MatchPayments $paymentsMatcher)
    {
        $this->paymentsMatcher = $paymentsMatcher;

        return $this;
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
    public function decode(&$error = null)
    {
        if (!$this->authChecker->check()) {
            $error = 'SDK Authentication failed';

            return null;
        }

        $strPushData = null;

        if (!empty($this->receiverCallback)) {
            // Closure::call doesn't exist in PHP5.6
            $strPushData = call_user_func($this->receiverCallback);
        } else {
            $strPushData = $this->receivePushNotification();
        }

        return json_decode( $strPushData, true );
    }

    /**
     * Filter transactions coming from push data, 
     * so that only inflow data remains
     *
     * @param  array        $transactions
     * @param  array|null   $inflowAmounts
     * @return array
     */
    public function filterInflows(
        $transactions, &$inflowAmounts = null
    )
    {
        // PHP < 7 do not support non null optional parameter
        $inflowAmounts = $inflowAmounts ? $inflowAmounts : [];
        $inflows = [];

        if (empty($transactions)) {
            return null;
        }

        // only CR
        foreach ($transactions as $trans) {
            if ($trans['type'] === 'CR') {
                $inflows[] = $trans;
                $inflowAmounts[] = $trans['amount'];
            }
        }

        return $inflows;
    }

    /**
     * @return array
     */
    public function handle()
    {
        $inflowAmounts = [];

        $handler = PushCallbackHandler::createDefault();
        $inflows = $handler->decode();

        $inflows = $handler->filterInflows($inflows, $inflowAmounts);

        if ( !empty($this->transFetcher) && !empty($this->paymentsMatcher) ) {
            $storedTransactions = $this->transFetcher->fetch($inflowAmounts);

            $payments = $this->paymentsMatcher
                ->match($inflows, $storedTransactions);

            return $payments;
        }

        return $inflows;
    }
}
