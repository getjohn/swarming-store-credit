<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Relation;

use Swarming\StoreCredit\Model\Transaction;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class CancelProcessor
{
    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsCustomerInterface
     */
    private $creditsCustomer;

    /**
     * @var \Swarming\StoreCredit\Helper\OrderStatusHistory
     */
    private $orderStatusHistoryHelper;

    /**
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
     * @param \Swarming\StoreCredit\Helper\OrderStatusHistory $orderStatusHistoryHelper
     */
    public function __construct(
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer,
        \Swarming\StoreCredit\Helper\OrderStatusHistory $orderStatusHistoryHelper
    ) {
        $this->creditsCurrency = $creditsCurrency;
        $this->creditsCustomer = $creditsCustomer;
        $this->orderStatusHistoryHelper = $orderStatusHistoryHelper;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @return void
     */
    public function process($order, $orderCredits)
    {
        $this->addCreditsTransaction($order, $orderCredits);
        $this->addOrderHistoryComment($order, $orderCredits);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @return void
     */
    private function addCreditsTransaction($order, $orderCredits)
    {
        $this->creditsCustomer->update(
            $order->getCustomerId(),
            [
                'type' => Transaction::TYPE_CANCEL,
                'order_id' => $orderCredits->getOrderId(),
                'amount' => ($orderCredits->getCredits() - $orderCredits->getCreditsPaid()),
                'summary' => __('Order {order}%1{order} cancellation', $order->getIncrementId())
            ]
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @return void
     */
    private function addOrderHistoryComment($order, $orderCredits)
    {
        $creditsFormatted = $this->creditsCurrency->format(
            ($orderCredits->getCredits() - $orderCredits->getCreditsPaid()),
            ConfigDisplay::FORMAT_HTML_FREE,
            $order->getStoreId()
        );

        $this->orderStatusHistoryHelper->addComment($order, __('Unheld amount of %1', $creditsFormatted));
    }
}
