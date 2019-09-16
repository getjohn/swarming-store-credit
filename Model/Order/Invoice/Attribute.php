<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Invoice;

use Swarming\StoreCredit\Model\ResourceModel\Order\Invoice\Attribute as ResourceModelInvoiceAttribute;

class Attribute
    extends \Magento\Framework\Model\AbstractModel
    implements \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModelInvoiceAttribute::class);
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
    public function getInvoiceId()
    {
        return $this->getData(self::INVOICE_ID);
    }

    /**
     * @param int $invoicesId
     * @return $this
     */
    public function setInvoiceId($invoicesId)
    {
        return $this->setData(self::INVOICE_ID, $invoicesId);
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
