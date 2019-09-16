<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CreditInterface extends ExtensibleDataInterface
{
    const CREDIT_ID = 'credit_id';
    const CUSTOMER_ID = 'customer_id';
    const BALANCE = 'balance';
    const TOTAL_PENDING = 'total_pending';
    const TOTAL_EARNED = 'total_earned';
    const TOTAL_SPENT = 'total_spent';
    const TOTAL_HELD = 'total_held';
    const LAST_ACTION = 'last_action';

    /**
     * @return int
     */
    public function getCreditId();

    /**
     * @param int $creditId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setCreditId($creditId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return float
     */
    public function getBalance();

    /**
     * @param float $balance
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setBalance($balance);

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function addBalance($amount);

    /**
     * @return float
     */
    public function getTotalPending();

    /**
     * @param float $totalPending
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setTotalPending($totalPending);

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function addTotalPending($amount);

    /**
     * @return float
     */
    public function getTotalEarned();

    /**
     * @param float $totalEarned
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setTotalEarned($totalEarned);

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function addTotalEarned($amount);

    /**
     * @return float
     */
    public function getTotalSpent();

    /**
     * @param float $totalSpent
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setTotalSpent($totalSpent);

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function addTotalSpent($amount);

    /**
     * @return float
     */
    public function getTotalHeld();

    /**
     * @param float $hold
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setTotalHeld($hold);

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function addTotalHeld($amount);

    /**
     * @return string
     */
    public function getLastAction();

    /**
     * @param string $date
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setLastAction($date);

    /**
     * @param string[] $keys
     * @return mixed[]
     */
    public function toArray(array $keys = []);

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditExtensionInterface $extensionAttributes
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setExtensionAttributes(CreditExtensionInterface $extensionAttributes);
}
