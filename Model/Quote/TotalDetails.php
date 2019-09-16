<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Quote;


class TotalDetails
    extends \Magento\Framework\Api\AbstractSimpleObject
    implements \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const CREDITS = 'credits';
    const MAX_ALLOWED_CREDITS = 'max_allowed_credits';
    const CREDITS_AMOUNT = 'credits_amount';
    const BASE_CREDITS_AMOUNT = 'base_credits_amount';
    /**#@-*/

    /**
     * @param string|float $credits
     * @return $this
     */
    public function setCredits($credits)
    {
        return $this->setData(self::CREDITS, $credits);
    }

    /**
     * @return float
     */
    public function getCredits()
    {
        return (float)$this->_get(self::CREDITS);
    }

    /**
     * @param float $maxCredits
     * @return $this
     */
    public function setMaxAllowedCredits($maxCredits)
    {
        return $this->setData(self::MAX_ALLOWED_CREDITS, $maxCredits);
    }

    /**
     * @return float
     */
    public function getMaxAllowedCredits()
    {
        return (float)$this->_get(self::MAX_ALLOWED_CREDITS);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setCreditsAmount($amount)
    {
        return $this->setData(self::CREDITS_AMOUNT, $amount);
    }

    /**
     * @return float
     */
    public function getCreditsAmount()
    {
        return (float)$this->_get(self::CREDITS_AMOUNT);
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setBaseCreditsAmount($amount)
    {
        return $this->setData(self::BASE_CREDITS_AMOUNT, $amount);
    }

    /**
     * @return float
     */
    public function getBaseCreditsAmount()
    {
        return (float)$this->_get(self::BASE_CREDITS_AMOUNT);
    }
}
