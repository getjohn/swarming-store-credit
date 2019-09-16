<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model;

use Swarming\StoreCredit\Api\Data\TransactionInterface;
use Swarming\StoreCredit\Api\Data\TransactionExtensionInterface;
use Swarming\StoreCredit\Model\ResourceModel\Transaction as TransactionResourceModel;
use Magento\Framework\Model\AbstractExtensibleModel;

class Transaction extends AbstractExtensibleModel implements \Swarming\StoreCredit\Api\Data\TransactionInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Source\TransactionTypes
     */
    private $transactionTypes;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->transactionTypes = $transactionTypes;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TransactionResourceModel::class);
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->_getData(self::TRANSACTION_ID);
    }

    /**
     * @param int $transactionId
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
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
    public function getAmount()
    {
        return (float)$this->_getData(self::AMOUNT);
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
     * @return float
     */
    public function getUsed()
    {
        return (float)$this->_getData(self::USED);
    }

    /**
     * @param float $usedAmount
     * @return $this
     */
    public function setUsed($usedAmount)
    {
        return $this->setData(self::USED, $usedAmount);
    }

    /**
     * @param float $usedAmount
     * @return $this
     */
    public function addUsed($usedAmount)
    {
        $this->setUsed($this->getUsed() + $usedAmount);
        return $this;
    }

    /**
     * @return float
     */
    public function getUnused()
    {
        return $this->getAmount() - $this->getUsed();
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->_getData(self::SUMMARY);
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary)
    {
        return $this->setData(self::SUMMARY, $summary);
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function addSummary($summary)
    {
        $savedSummary = (string)$this->getSummary();
        $summary = $savedSummary . ($savedSummary != '' ? "\n" : '') . $summary;
        return $this->setSummary($summary);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->getType() ? $this->transactionTypes->getLabel($this->getType()) : '';
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
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
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->_getData(self::INVOICE_ID);
    }

    /**
     * @param int $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId)
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    /**
     * @return int
     */
    public function getCreditmemoId()
    {
        return $this->_getData(self::CREDITMEMO_ID);
    }

    /**
     * @param int $creditmemoId
     * @return $this
     */
    public function setCreditmemoId($creditmemoId)
    {
        return $this->setData(self::CREDITMEMO_ID, $creditmemoId);
    }

    /**
     * @return bool
     */
    public function getSuppressNotification()
    {
        return (bool)$this->_getData(self::SUPPRESS_NOTIFICATION);
    }

    /**
     * @param bool $suppressNotification
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function setSuppressNotification($suppressNotification)
    {
        return $this->setData(self::SUPPRESS_NOTIFICATION, $suppressNotification);
    }

    /**
     * @return string
     */
    public function getAtTime()
    {
        return $this->_getData(self::AT_TIME);
    }

    /**
     * @param string $atTime
     * @return $this
     */
    public function setAtTime($atTime)
    {
        return $this->setData(self::AT_TIME, $atTime);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\TransactionExtensionInterface
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
        $extensionAttributes = $this->extensionAttributesFactory->create(TransactionInterface::class);
        $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(TransactionExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
