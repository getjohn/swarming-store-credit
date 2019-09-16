<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Config;

class Notification extends \Swarming\StoreCredit\Model\Config\General
{
    /**
     * @param int|null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->getStoreValue('swarming_credits/notification/email_sender', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function isBalanceUpdateNotify($storeId = null)
    {
        return $this->getStoreValue('swarming_credits/notification/balance_update_notify', $storeId);
    }

    /**
     * @param string $type
     * @param int|null $storeId
     * @return bool
     */
    public function isTransactionNotify($type, $storeId = null)
    {
        $types = $this->getStoreValue('swarming_credits/notification/transactions_notify', $storeId);
        return $this->isBalanceUpdateNotify($storeId) ? in_array($type, explode(',', $types)) : false;
    }

    /**
     * @param string $type
     * @param int|null $storeId
     * @return string
     */
    public function getTransactionTemplate($type, $storeId = null)
    {
        return $this->getStoreValue("swarming_credits/notification/transactions_{$type}_template", $storeId);
    }
}
