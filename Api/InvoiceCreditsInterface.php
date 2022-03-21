<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

use Magento\Sales\Model\Order\Invoice;

/**
 * @api
 */
interface InvoiceCreditsInterface
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return float
     */
    public function getMaxAllowed(Invoice $invoice);
}
