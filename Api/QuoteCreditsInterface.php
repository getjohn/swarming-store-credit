<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface QuoteCreditsInterface
{
    /**
     * @param \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote $cart
     * @param float $originGrandTotal
     * @return float
     */
    public function getMaxAllowed($cart, $originGrandTotal = null);
}
