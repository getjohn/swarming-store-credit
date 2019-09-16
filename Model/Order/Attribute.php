<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order;

use Swarming\StoreCredit\Model\ResourceModel\Order\Attribute as ResourceModelOrderAttribute;

class Attribute
    extends \Magento\Framework\Model\AbstractModel
    implements \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelOrderAttribute::class);
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * @param int $attributeId
     * @return $this
     */
    public function setAttributeId($attributeId)
    {
        return $this->setData(self::ATTRIBUTE_ID, $attributeId);
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @return float
     */
    public function getCredits()
    {
        return (float)$this->getData(self::CREDITS);
    }

    /**
     * @param float $credits
     * @return $this
     */
    public function setCredits($credits)
    {
        return $this->setData(self::CREDITS, $credits);
    }

    /**
     * @return float
     */
    public function getCreditsPaid()
    {
        return (float)$this->getData(self::CREDITS_PAID);
    }

    /**
     * @param float $creditsPaid
     * @return $this
     */
    public function setCreditsPaid($creditsPaid)
    {
        return $this->setData(self::CREDITS_PAID, $creditsPaid);
    }

    /**
     * @return float
     */
    public function getCreditsRefunded()
    {
        return (float)$this->getData(self::CREDITS_REFUNDED);
    }

    /**
     * @param float $creditsRefunded
     * @return $this
     */
    public function setCreditsRefunded($creditsRefunded)
    {
        return $this->setData(self::CREDITS_REFUNDED, $creditsRefunded);
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return (float)$this->getData(self::AMOUNT);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @return float
     */
    public function getAmountPaid()
    {
        return (float)$this->getData(self::AMOUNT_PAID);
    }

    /**
     * @param float $amountPaid
     * @return $this
     */
    public function setAmountPaid($amountPaid)
    {
        return $this->setData(self::AMOUNT_PAID, $amountPaid);
    }

    /**
     * @return float
     */
    public function getAmountRefunded()
    {
        return (float)$this->getData(self::AMOUNT_REFUNDED);
    }

    /**
     * @param float $amountRefunded
     * @return $this
     */
    public function setAmountRefunded($amountRefunded)
    {
        return $this->setData(self::AMOUNT_REFUNDED, $amountRefunded);
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return (float)$this->getData(self::BASE_AMOUNT);
    }

    /**
     * @param float $baseAmount
     * @return $this
     */
    public function setBaseAmount($baseAmount)
    {
        return $this->setData(self::BASE_AMOUNT, $baseAmount);
    }

    /**
     * @return float
     */
    public function getBaseAmountPaid()
    {
        return (float)$this->getData(self::BASE_AMOUNT_PAID);
    }

    /**
     * @param float $baseAmountPaid
     * @return $this
     */
    public function setBaseAmountPaid($baseAmountPaid)
    {
        return $this->setData(self::BASE_AMOUNT_PAID, $baseAmountPaid);
    }

    /**
     * @return float
     */
    public function getBaseAmountRefunded()
    {
        return (float)$this->getData(self::BASE_AMOUNT_REFUNDED);
    }

    /**
     * @param float $baseAmountRefunded
     * @return $this
     */
    public function setBaseAmountRefunded($baseAmountRefunded)
    {
        return $this->setData(self::BASE_AMOUNT_REFUNDED, $baseAmountRefunded);
    }
}
