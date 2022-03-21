<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface QuoteAttributeManagementInterface
{
    /**
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function getForCart($cart);
}
