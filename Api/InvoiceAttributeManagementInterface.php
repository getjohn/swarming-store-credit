<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
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
