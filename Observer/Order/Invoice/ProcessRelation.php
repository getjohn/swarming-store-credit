<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order\Invoice;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Api\Data\InvoiceInterface;

class ProcessRelation implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
     */
    private $invoiceAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Model\Order\Invoice\ProcessorRelation
     */
    private $invoiceRelationProcessor;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
     * @param \Swarming\StoreCredit\Model\Order\Invoice\ProcessorRelation $invoiceRelationProcessor
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement,
        \Swarming\StoreCredit\Model\Order\Invoice\ProcessorRelation $invoiceRelationProcessor
    ) {
        $this->configGeneral = $configGeneral;
        $this->invoiceAttributeManagement = $invoiceAttributeManagement;
        $this->invoiceRelationProcessor = $invoiceRelationProcessor;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return bool
     */
    private function isPaid($invoice)
    {
        return $invoice->getState() == Invoice::STATE_PAID
            && $invoice->getOrigData(InvoiceInterface::STATE) != Invoice::STATE_PAID;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData('object');

        if (!$this->configGeneral->isActive($invoice->getStoreId()) || !$invoice->getOrder()->getCustomerId()) {
            return;
        }

        if (!$this->isPaid($invoice)) {
            return;
        }

        $invoiceCredits = $this->invoiceAttributeManagement->getForInvoice($invoice);
        if ($invoiceCredits->getCredits() < 0.01) {
            return;
        }

        $this->invoiceRelationProcessor->process($invoice, $invoiceCredits);
    }
}
