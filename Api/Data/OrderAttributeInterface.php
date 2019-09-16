<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

interface OrderAttributeInterface
{
    const ATTRIBUTE_ID = 'attribute_id';
    const ORDER_ID = 'order_id';
    const CREDITS = 'credits';
    const CREDITS_PAID = 'credits_paid';
    const CREDITS_REFUNDED = 'credits_refunded';
    const AMOUNT = 'amount';
    const AMOUNT_PAID = 'amount_paid';
    const AMOUNT_REFUNDED = 'amount_refunded';
    const BASE_AMOUNT = 'base_amount';
    const BASE_AMOUNT_PAID = 'base_amount_paid';
    const BASE_AMOUNT_REFUNDED = 'base_amount_refunded';

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $attributeId
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setOrderId($orderId);

    /**
     * @return float
     */
    public function getCredits();

    /**
     * @param float $credits
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setCredits($credits);

    /**
     * @return float
     */
    public function getCreditsPaid();

    /**
     * @param float $creditsPaid
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setCreditsPaid($creditsPaid);

    /**
     * @return float
     */
    public function getCreditsRefunded();

    /**
     * @param float $creditsRefunded
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setCreditsRefunded($creditsRefunded);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getAmountPaid();

    /**
     * @param float $amountPaid
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setAmountPaid($amountPaid);

    /**
     * @return float
     */
    public function getAmountRefunded();

    /**
     * @param float $amountRefunded
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setAmountRefunded($amountRefunded);

    /**
     * @return float
     */
    public function getBaseAmount();

    /**
     * @param float $baseAmount
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setBaseAmount($baseAmount);

    /**
     * @return float
     */
    public function getBaseAmountPaid();

    /**
     * @param float $baseAmountPaid
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setBaseAmountPaid($baseAmountPaid);

    /**
     * @return float
     */
    public function getBaseAmountRefunded();

    /**
     * @param float $baseAmountRefunded
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function setBaseAmountRefunded($baseAmountRefunded);
}
