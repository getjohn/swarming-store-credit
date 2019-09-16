<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model;

use Swarming\StoreCredit\Api\Data\CreditInterface;
use Swarming\StoreCredit\Api\Data\CreditExtensionInterface;
use Swarming\StoreCredit\Model\ResourceModel\Credit as ResourceModelCredit;

class Credit
    extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Swarming\StoreCredit\Api\Data\CreditInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelCredit::class);
    }

    /**
     * @return int
     */
    public function getCreditId()
    {
        return $this->_getData(self::CREDIT_ID);
    }

    /**
     * @param int $creditId
     * @return $this
     */
    public function setCreditId($creditId)
    {
        return $this->setData(self::CREDIT_ID, $creditId);
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getData(self::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return (float)$this->_getData(self::BALANCE);
    }

    /**
     * @param float $balance
     * @return $this
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function addBalance($amount)
    {
        $this->setBalance($this->getBalance() + $amount);
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPending()
    {
        return (float)$this->_getData(self::TOTAL_PENDING);
    }

    /**
     * @param float $totalPending
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function setTotalPending($totalPending)
    {
        return $this->setData(self::TOTAL_PENDING, $totalPending);
    }

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function addTotalPending($amount)
    {
        return $this->setTotalPending($this->getTotalPending() + $amount);
    }

    /**
     * @return float
     */
    public function getTotalEarned()
    {
        return (float)$this->_getData(self::TOTAL_EARNED);
    }

    /**
     * @param float $totalEarned
     * @return $this
     */
    public function setTotalEarned($totalEarned)
    {
        return $this->setData(self::TOTAL_EARNED, $totalEarned);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function addTotalEarned($amount)
    {
        $this->setTotalEarned($this->getTotalEarned() + $amount);
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalSpent()
    {
        return (float)$this->_getData(self::TOTAL_SPENT);
    }

    /**
     * @param  float $totalSpent
     * @return $this
     */
    public function setTotalSpent($totalSpent)
    {
        return $this->setData(self::TOTAL_SPENT, $totalSpent);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function addTotalSpent($amount)
    {
        $this->setTotalSpent($this->getTotalSpent() + $amount);
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHeld()
    {
        return (float)$this->_getData(self::TOTAL_HELD);
    }

    /**
     * @param float $hold
     * @return $this
     */
    public function setTotalHeld($hold)
    {
        return $this->setData(self::TOTAL_HELD, $hold);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function addTotalHeld($amount)
    {
        $this->setTotalHeld($this->getTotalHeld() + $amount);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastAction()
    {
        return $this->_getData(self::LAST_ACTION);
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setLastAction($date)
    {
        return $this->setData(self::LAST_ACTION, $date);
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditExtensionInterface
     */
    public function getExtensionAttributes()
    {
        if (!$this->_getExtensionAttributes()) {
            $this->initExtensionAttributes();
        }
        return $this->_getExtensionAttributes();
    }

    /**
     * @return void
     */
    private function initExtensionAttributes()
    {
        $extensionAttributes = $this->extensionAttributesFactory->create(CreditInterface::class);
        $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(CreditExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
