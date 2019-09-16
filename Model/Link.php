<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model;

use Swarming\StoreCredit\Api\Data\LinkInterface;
use Swarming\StoreCredit\Api\Data\LinkExtensionInterface;
use Swarming\StoreCredit\Model\ResourceModel\Link as LinkResourceModel;

class Link
    extends \Magento\Framework\Model\AbstractExtensibleModel
    implements \Swarming\StoreCredit\Api\Data\LinkInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(LinkResourceModel::class);
    }

    /**
     * @return int
     */
    public function getLinkId()
    {
        return $this->_getData(self::LINK_ID);
    }

    /**
     * @param int $linkId
     * @return $this
     */
    public function setLinkId($linkId)
    {
        return $this->setData(self::LINK_ID, $linkId);
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
    public function getTransactionLinkId()
    {
        return $this->_getData(self::TRANSACTION_LINK_ID);
    }

    /**
     * @param int $transactionId
     * @return $this
     */
    public function setTransactionLinkId($transactionId)
    {
        return $this->setData(self::TRANSACTION_LINK_ID, $transactionId);
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
     * @return \Swarming\StoreCredit\Api\Data\LinkExtensionInterface
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
        $extensionAttributes = $this->extensionAttributesFactory->create(LinkInterface::class);
        $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(LinkExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
