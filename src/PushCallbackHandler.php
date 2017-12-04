<?php namespace Moota\SDK;

use Moota\SDK\Contracts\Push\FetchesOrders;
use Moota\SDK\Contracts\Push\MatchesOrders;
use Moota\SDK\Contracts\Push\FulfillsOrder;
use Moota\SDK\Contracts\Push\FindsDuplicate;

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

    /** @var FulfillsOrder $orderFulfiller */
    protected $orderFulfiller;

    /** @var FindsDuplicate $dupeFinder */
    protected $dupeFinder;

    /**
     * @param Auth $authChecker
     * @param \Closure|null $receiverCallback
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

    public function setOrderFulfiller(FulfillsOrder $fulfiller)
    {
        $this->orderFulfiller = $fulfiller;

        return $this;
    }

    public function setDupeFinder(FindsDuplicate $dupeFinder)
    {
        $this->dupeFinder = $dupeFinder;

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
        $inflowAmounts = $inflowAmounts ?: array();
        $inflows = array();

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
     * Handles Bank Account Mutation data that was pushed by Moota
     *
     * @return array
     */
    public function handle()
    {
        $inflowAmounts = [];

        try {
            $inflows = $this->decode();
            $inflows = $this->filterInflows($inflows, $inflowAmounts);

            $savedCount = 0;

            if ( !empty($this->orderFetcher) && !empty($this->orderMatcher) ) {
                $storedOrders = $this->orderFetcher->fetch($inflowAmounts);

                if ( !empty($this->dupeFinder) ) {
                    $this->dupeFinder->findDupes($inflows, $storedOrders);
                }

                $payments = $this->orderMatcher->match($inflows, $storedOrders);

                foreach ($payments as $payment) {
                    if ( $this->orderFulfiller->fulfill($payment) ) {
                        $savedCount++;
                    }
                }
            }

            if ($savedCount > 0) {
                return array(
                    'status' => 'ok', 'count' => $savedCount,
                );
            } else {
                return array(
                    'status' => 'not-found',
                    'message' => 'No matching order found',
                );
            }
        } catch (\Exception $ex) {
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

    /**
     * Converts `PushCallbackHandler#handle` response array into
     * Http Status Code
     *
     * @param array
     * @return int
     */
    public static function statusDataToHttpCode($statusData) {
        if (empty($statusData['status'])) {
            return 500;
        }

        switch ($statusData['status']) {
            case 'not-found': return 404;
            case 'error': return 500;
            default: return 200;
        }
    }
}
