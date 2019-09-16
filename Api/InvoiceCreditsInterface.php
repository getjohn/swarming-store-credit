<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
