<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface LinkInterface extends ExtensibleDataInterface
{
    const LINK_ID = 'link_id';
    const TRANSACTION_ID = 'transaction_id';
    const TRANSACTION_LINK_ID = 'transaction_link_id';
    const ORDER_ID = 'order_id';
    const AMOUNT = 'amount';

    /**
     * @return int
     */
    public function getLinkId();

    /**
     * @param int $linkId
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function setLinkId($linkId);

    /**
     * @return int
     */
    public function getTransactionId();

    /**
     * @param int $transactionId
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function setTransactionId($transactionId);

    /**
     * @return int
     */
    public function getTransactionLinkId();

    /**
     * @param int $transactionId
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function setTransactionLinkId($transactionId);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $orderId
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function setOrderId($orderId);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function setAmount($amount);

    /**
     * @return \Swarming\StoreCredit\Api\Data\LinkExtensionInterface
     */
    public function getExtensionAttributes();

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkExtensionInterface $extensionAttributes
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function setExtensionAttributes(LinkExtensionInterface $extensionAttributes);
}
