<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Order\Invoice;

use Magento\Sales\Api\Data\InvoiceInterface;

class AttributeManagement implements \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface
     */
    private $invoiceAttributeRepository;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->invoiceAttributeRepository = $invoiceAttributeRepository;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     */
    public function getForInvoice($invoice)
    {
        $invoiceAttributes = $invoice->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(InvoiceInterface::class);
        $invoice->setExtensionAttributes($invoiceAttributes);

        $invoiceCredits = $invoiceAttributes->getCredits();
        if (!$invoiceCredits) {
            $invoiceCredits = $invoice->getEntityId()
                ? $this->invoiceAttributeRepository->getByInvoiceId($invoice->getEntityId(), true)
                : $this->invoiceAttributeRepository->getNew();

            $invoiceAttributes->setCredits($invoiceCredits);
        }

        $invoiceCredits->setInvoiceId($invoice->getEntityId());
        return $invoiceCredits;
    }
}
