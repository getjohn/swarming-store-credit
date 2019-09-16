<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

interface QuoteAttributeInterface
{
    const ATTRIBUTE_ID = 'attribute_id';
    const QUOTE_ID = 'quote_id';
    const AVAILABLE = 'available';
    const CREDITS = 'credits';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param int $quoteId
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function setQuoteId($quoteId);

    /**
     * @return float
     */
    public function getAvailable();

    /**
     * @param float $available
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function setAvailable($available);

    /**
     * @return float
     */
    public function getCredits();

    /**
     * @param float $credits
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function setCredits($credits);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getBaseAmount();

    /**
     * @param float $baseAmount
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function setBaseAmount($baseAmount);
}
