<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales\Order;

use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceInterface;

class InvoiceRepository
{
    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface
     */
    private $invoiceAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
    ) {
        $this->invoiceAttributeRepository = $invoiceAttributeRepository;
    }

    /**
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(InvoiceRepositoryInterface $subject, \Closure $proceed, InvoiceInterface $invoice)
    {
        $invoiceExtension = $invoice->getExtensionAttributes();

        $proceed($invoice);

        if ($invoiceExtension && $invoiceExtension->getCredits()) {
            $invoiceCredits = $invoiceExtension->getCredits();
            $invoiceCredits->setInvoiceId($invoice->getEntityId());
            $this->invoiceAttributeRepository->save($invoiceCredits);
        }

        return $invoice;
    }
}
