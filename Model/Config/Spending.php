<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Config;

class Spending extends \Swarming\StoreCredit\Model\Config\General
{
    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabledLimit($storeId = null)
    {
        return $this->isSetStoreFlag('swarming_credits/spending/enable_limit', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getErrorMessage($storeId = null)
    {
        return $this->getStoreValue('swarming_credits/spending/error_message', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getLimitType($storeId = null)
    {
        return $this->getStoreValue('swarming_credits/spending/limit_type', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getPercentageLimit($storeId = null)
    {
        $spendMaxPercentage = (int)$this->getStoreValue('swarming_credits/spending/spend_percent', $storeId);
        return max(0, min(100, $spendMaxPercentage));
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getFixedLimit($storeId = null)
    {
        return (int)$this->getStoreValue('swarming_credits/spending/spend_fixed', $storeId);
    }
}
