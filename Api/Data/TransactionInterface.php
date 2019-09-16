<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface TransactionInterface extends ExtensibleDataInterface
{
    /**
     * Transaction types
     */
    const TYPE_ADD = 'add';
    const TYPE_SUBTRACT = 'subtract';
    const TYPE_HOLD = 'hold';
    const TYPE_CANCEL = 'cancel';
    const TYPE_SPEND = 'spend';
    const TYPE_REFUND = 'refund';

    const TRANSACTION_ID = 'transaction_id';
    const CUSTOMER_ID = 'customer_id';
    const AMOUNT = 'amount';
    const BALANCE = 'balance';
    const USED = 'used';
    const SUMMARY = 'summary';
    const TYPE = 'type';
    const ORDER_ID = 'order_id';
    const INVOICE_ID = 'invoice_id';
    const CREDITMEMO_ID = 'creditmemo_id';
    const SUPPRESS_NOTIFICATION = 'suppress_notification';
    const AT_TIME = 'at_time';
    const STATUS = 'status';

    /**
     * @return int
     */
    public function getTransactionId();

    /**
     * @param int $transactionId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setTransactionId($transactionId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getBalance();

    /**
     * @param float $balance
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setBalance($balance);

    /**
     * @return float
     */
    public function getUsed();

    /**
     * @param float $usedAmount
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setUsed($usedAmount);

    /**
     * @param float $usedAmount
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function addUsed($usedAmount);

    /**
     * @return float
     */
    public function getUnused();

    /**
     * @return string
     */
    public function getSummary();

    /**
     * @param string $summary
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setSummary($summary);

    /**
     * @param string $summary
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function addSummary($summary);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getTypeLabel();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setOrderId($orderId);

    /**
     * @return int
     */
    public function getInvoiceId();

    /**
     * @param int $invoiceId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setInvoiceId($invoiceId);

    /**
     * @return int
     */
    public function getCreditmemoId();

    /**
     * @param int $creditmemoId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setCreditmemoId($creditmemoId);

    /**
     * @return bool
     */
    public function getSuppressNotification();

    /**
     * @param bool $suppressNotification
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setSuppressNotification($suppressNotification);

    /**
     * @return string
     */
    public function getAtTime();

    /**
     * @param string $atTime
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setAtTime($atTime);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setStatus($status);

    /**
     * @return \Swarming\StoreCredit\Api\Data\TransactionExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionExtensionInterface $extensionAttributes
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setExtensionAttributes(TransactionExtensionInterface $extensionAttributes);
}
