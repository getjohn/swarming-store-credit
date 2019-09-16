<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order\Invoice;

class AfterSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface
     */
    private $invoiceAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
    ) {
        $this->configGeneral = $configGeneral;
        $this->invoiceAttributeRepository = $invoiceAttributeRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData('invoice');

        if (!$invoice || !$this->configGeneral->isActive($invoice->getStoreId())) {
            return;
        }

        $invoiceExtension = $invoice->getExtensionAttributes();

        if ($invoiceExtension && $invoiceExtension->getCredits() && $invoiceExtension->getCredits()->getCredits() >= 0.1) {
            $invoiceCredits = $invoiceExtension->getCredits();
            $invoiceCredits->setInvoiceId($invoice->getEntityId());
            $this->invoiceAttributeRepository->save($invoiceCredits);
        }
    }
}
