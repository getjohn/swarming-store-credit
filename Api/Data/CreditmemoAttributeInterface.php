<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

interface CreditmemoAttributeInterface
{
    const ATTRIBUTE_ID = 'attribute_id';
    const CREDITMEMO_ID = 'creditmemo_id';
    const CREDITS = 'credits';
    const CREDITS_REFUNDED = 'credits_refunded';
    const AMOUNT = 'amount';
    const AMOUNT_REFUNDED = 'amount_refunded';
    const BASE_AMOUNT = 'base_amount';
    const BASE_AMOUNT_REFUNDED = 'base_amount_refunded';

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @return int
     */
    public function getCreditmemoId();

    /**
     * @param int $creditmemoId
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setCreditmemoId($creditmemoId);

    /**
     * @return float
     */
    public function getCredits();

    /**
     * @param float $credits
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setCredits($credits);

    /**
     * @return float
     */
    public function getCreditsRefunded();

    /**
     * @param float $creditsRefunded
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setCreditsRefunded($creditsRefunded);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getAmountRefunded();

    /**
     * @param float $amountRefunded
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setAmountRefunded($amountRefunded);

    /**
     * @return float
     */
    public function getBaseAmount();

    /**
     * @param float $baseAmount
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setBaseAmount($baseAmount);

    /**
     * @return float
     */
    public function getBaseAmountRefunded();

    /**
     * @param float $baseAmountRefunded
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function setBaseAmountRefunded($baseAmountRefunded);
}
