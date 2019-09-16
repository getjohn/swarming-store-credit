<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales\Order\Invoice;

use Magento\Sales\Model\Order\Invoice;

class CanRefund
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
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
     */
    private $invoiceAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
    ) {
        $this->configRefund = $configRefund;
        $this->priceCurrency = $priceCurrency;
        $this->invoiceAttributeManagement = $invoiceAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $subject
     * @param bool $canRefund
     * @return bool
     */
    public function afterCanRefund($subject, $canRefund)
    {
        $storeId = $subject->getStoreId();
        if ($this->configRefund->isActive($storeId)
            && $this->configRefund->isRefundEnabled($storeId)
            && $subject->getState() == Invoice::STATE_PAID
        ) {
            $invoiceAttribute = $this->invoiceAttributeManagement->getForInvoice($subject);

            $baseAmount = abs($invoiceAttribute->getBaseAmount()) + $subject->getBaseGrandTotal();
            $baseAmountRefunded = $invoiceAttribute->getBaseAmountRefunded() + $subject->getBaseTotalRefunded();

            $canRefund = !(abs($baseAmount - $baseAmountRefunded) < .0001);
        }
        return $canRefund;
    }
}
