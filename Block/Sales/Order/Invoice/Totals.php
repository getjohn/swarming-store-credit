<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Sales\Order\Invoice;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Swarming\StoreCredit\Model\Quote\Address\Total\Credits as QuoteTotalCredits;

class Totals extends \Swarming\StoreCredit\Block\Sales\Order\Totals
{
    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
     */
    protected $invoiceAttributeManagement;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Magento\Framework\DataObject\Factory $dataObjectFactory
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement,
        array $data = []
    ) {
        $this->invoiceAttributeManagement = $invoiceAttributeManagement;
        parent::__construct(
            $context,
            $configDisplay,
            $creditsCurrency,
            $priceCurrency,
            $dataObjectFactory,
            $orderAttributeManagement,
            $data
        );
    }

    /**
     * @return \Magento\Sales\Block\Order\Invoice\Totals|\bool
     */
    protected function getOrderTotals()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock instanceof \Magento\Sales\Block\Order\Invoice\Totals) {
            return $parentBlock;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $orderTotals = $this->getOrderTotals();
        if (!$orderTotals) {
            return $this;
        }

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $orderTotals->getInvoice();
        $this->addInvoiceTotal($invoice, $orderTotals);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addInvoiceTotal($invoice, $orderTotals)
    {
        $invoiceCredits = $this->invoiceAttributeManagement->getForInvoice($invoice);
        if ($invoiceCredits->getCredits() == 0) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => QuoteTotalCredits::CODE,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($invoiceCredits->getCredits(), ConfigDisplay::FORMAT_TOTAL, $invoice->getStoreId(), $invoiceCredits->getAmount()),
                'base_value' => $this->priceCurrency->format($invoiceCredits->getBaseAmount(), false),
                'label' => $this->configDisplay->getName($invoice->getStoreId())
            ])
        );
    }
}
