<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Config;

class Expiration extends \Swarming\StoreCredit\Model\Config\General
{
    /**
     * @param int|null $storeId
     * @return int
     */
    public function getLifeTime($storeId = null)
    {
        return (int)$this->getProtectedValue('swarming_credits/expiration/life_time', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getExpirationReminderDays($storeId = null)
    {
        return $this->getLifeTime($storeId) > 0
            ? (int)$this->getStoreValue('swarming_credits/expiration/expiration_reminder_days', $storeId)
            : 0;
    }

    /**
     * @param int|null $storeId
     * @return string|false
     */
    public function getExpirationRepeats($storeId = null)
    {
        return $this->getExpirationReminderDays($storeId)
            ? $this->getStoreValue('swarming_credits/expiration/expiration_repeats', $storeId)
            : false;
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getExpirationTemplate($storeId = null)
    {
        return $this->getStoreValue('swarming_credits/expiration/expiration_template', $storeId);
    }
}
