<?php namespace Moota\SDK;

use Moota\SDK\Contracts\Push\FetchesOrders;
use Moota\SDK\Contracts\Push\MatchesOrders;
use Moota\SDK\Contracts\Push\FullfilsOrder;

class PushCallbackHandler
{
    /** @var Auth $authChecker */
    protected $authChecker;

    /** @var \Closure|null $receiverCallback */
    protected $receiverCallback;

    /** @var FetchesOrders $orderFetcher */
    protected $orderFetcher;

    /** @var MatchesOrders $orderMatcher */
    protected $orderMatcher;

    /** @var FullfilsOrder $orderFullfiler */
    protected $orderFullfiler;

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

    public function setOrderFetcher(FetchesOrders $fetcher)
    {
        $this->orderFetcher = $fetcher;

        return $this;
    }

    public function setOrderMatcher(MatchesOrders $matcher)
    {
        $this->orderMatcher = $matcher;

        return $this;
    }

    public function setOrderFullfiler(FullfilsOrder $fullfiler)
    {
        $this->orderFullfiler = $fullfiler;

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

        $inflows = $this->decode();
        $inflows = $this->filterInflows($inflows, $inflowAmounts);
        $savedCount = 0;
        $statusData = array(
            'status' => 'not-ok', 'message' => 'No matching order found'
        );

        if ( !empty($this->orderFetcher) && !empty($this->orderMatcher) ) {
            $storedOrders = $this->orderFetcher->fetch($inflowAmounts);

            $payments = $this->orderMatcher
                ->match($inflows, $storedOrders);

            foreach ($payments as $payment) {
                if ($this->orderFullfiler->fullfil($payment)) {
                    $savedCount++;
                }
            }
        }

        if ($savedCount > 0) {
            $statusData = array(
                'status' => 'ok', 'count' => $savedCount
            );
        }

        return $statusData;
    }
}
