<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Magento\Sales\Model\Order\Invoice;

class InvoiceCredits implements \Swarming\StoreCredit\Api\InvoiceCreditsInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     */
    public function __construct(
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency
    ) {
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->creditsCurrency = $creditsCurrency;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return float
     */
    public function getMaxAllowed(Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);

        $creditsBaseAmountAvailable = abs($orderCredits->getBaseAmount()) - abs($orderCredits->getBaseAmountPaid());
        $creditsBaseAmount = min($invoice->getBaseGrandTotal(), $creditsBaseAmountAvailable);

        $creditsAvailable = $orderCredits->getCredits() - $orderCredits->getCreditsPaid();
        $credits = min(
            $this->creditsCurrency->convertBaseToCredits($creditsBaseAmount, $order->getStoreId(), true),
            $creditsAvailable
        );

        return $credits;
    }
}
