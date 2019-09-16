<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
