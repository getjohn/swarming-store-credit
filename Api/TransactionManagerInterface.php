<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

interface TransactionManagerInterface
{
    /**
     * @param int $customerId
     * @return float
     */
    public function getBalance($customerId);

    /**
     * @param int $customerId
     * @return string
     */
    public function getTotalSpent($customerId);

    /**
     * @param int $customerId
     * @return string
     */
    public function getTotalHeld($customerId);

    /**
     * @param int[] $customerIds
     * @param int $lifeTime
     * @param int $expirationReminderDays
     * @param string $expirationRepeats
     * @return array
     */
    public function getCustomersExpirationAmounts($customerIds, $lifeTime, $expirationReminderDays, $expirationRepeats);
}
