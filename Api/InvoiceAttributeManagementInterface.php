<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface InvoiceAttributeManagementInterface
{
    /**
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     */
    public function getForInvoice($invoice);
}
