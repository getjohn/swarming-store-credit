<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\Config;

class Refund extends \Swarming\StoreCredit\Model\Config\General
{
    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isRefundEnabled($storeId = null)
    {
        return $this->isSetStoreFlag('swarming_credits/refund/refund_enabled', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isRefundCurrencyEnabled($storeId = null)
    {
        return $this->isSetStoreFlag('swarming_credits/refund/refund_currency_enabled', $storeId);
    }
}
