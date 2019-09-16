<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales\Order;

use Magento\Sales\Model\Order;

class CanCreditmemo
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Refund
     */
    private $configRefund;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
    ) {
        $this->configRefund = $configRefund;
        $this->priceCurrency = $priceCurrency;
        $this->orderAttributeManagement = $orderAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Model\Order $subject
     * @param bool $canCreditmemo
     * @return bool
     */
    public function afterCanCreditmemo($subject, $canCreditmemo)
    {
        if (!$this->configRefund->isRefundEnabled($subject->getStoreId())) {
            return $canCreditmemo;
        }

        if ($subject->hasForcedCanCreditmemo()) {
            return $subject->getForcedCanCreditmemo();
        }

        if ($subject->canUnhold() || $subject->isPaymentReview()) {
            return false;
        }

        if ($subject->isCanceled() || $subject->getState() === Order::STATE_CLOSED) {
            return false;
        }

        $orderCredits = $this->orderAttributeManagement->getForOrder($subject);
        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->priceCurrency->round($subject->getTotalPaid()) + abs($orderCredits->getBaseAmountPaid())
                - $subject->getTotalRefunded() - $orderCredits->getBaseAmountRefunded()) < .0001
        ) {
            return false;
        }

        if ($subject->getActionFlag(Order::ACTION_FLAG_EDIT) === false) {
            return false;
        }
        return true;
    }
}
