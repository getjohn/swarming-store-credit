<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
