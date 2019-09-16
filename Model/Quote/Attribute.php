<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Quote;

use Swarming\StoreCredit\Model\ResourceModel\Quote\Attribute as ResourceModelQuoteAttribute;

class Attribute
    extends \Magento\Framework\Model\AbstractModel
    implements \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelQuoteAttribute::class);
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
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        return $this->setData(self::QUOTE_ID, $quoteId);
    }

    /**
     * @return float
     */
    public function getAvailable()
    {
        return (float)$this->getData(self::AVAILABLE);
    }

    /**
     * @param float $available
     * @return $this
     */
    public function setAvailable($available)
    {
        return $this->setData(self::AVAILABLE, $available);
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
}
