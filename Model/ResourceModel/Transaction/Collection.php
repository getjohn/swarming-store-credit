<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Transaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Swarming\StoreCredit\Model\Transaction;
use Swarming\StoreCredit\Model\ResourceModel\Transaction as ResourceModelTransaction;

class Collection extends AbstractCollection
{
    /**
     * @var \Swarming\StoreCredit\Helper\Transaction
     */
    private $transactionHelper;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Swarming\StoreCredit\Helper\Transaction $transactionHelper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Swarming\StoreCredit\Helper\Transaction $transactionHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->transactionHelper = $transactionHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Transaction::class, ResourceModelTransaction::class);
    }

    /**
     * @param int $customerId
     * @param int $lifeTime
     * @return $this
     */
    public function filterAvailable($customerId, $lifeTime)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        $this->addFieldToFilter('type', ['in' => $this->transactionHelper->getGainTypes()]);

        if ($lifeTime > 0) {
            $this->getSelect()->where("DATE(`at_time`) > DATE_SUB(now(), INTERVAL {$lifeTime} DAY)");
        }
        $this->getSelect()->where('amount > used');
        $this->setOrder('at_time', self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $orderId
     * @return $this
     */
    public function filterHeld($customerId, $orderId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        $this->addFieldToFilter('type', Transaction::TYPE_HOLD);
        $this->addFieldToFilter('order_id', $orderId);
        $this->getSelect()->where('amount > used');
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $orderId
     * @return $this
     */
    public function filterSpent($customerId, $orderId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        $this->addFieldToFilter('type', Transaction::TYPE_SPEND);
        $this->addFieldToFilter('order_id', $orderId);
        $this->getSelect()->where('amount > used');
        return $this;
    }

    /**
     * @param int $customerId
     * @param int $orderId
     * @return $this
     */
    public function filterForRefund($customerId, $orderId)
    {
        $this->addFieldToFilter('customer_id', $customerId);
        $this->addFieldToFilter('type', ['in' => $this->transactionHelper->getGainTypes()]);
        $this->join(['l' => 'swarming_credit_link'], 'main_table.transaction_id = l.transaction_link_id', null);
        $this->getSelect()->where('l.order_id = ?', $orderId);
        $this->setOrder('at_time', self::SORT_ORDER_DESC);
        $this->getSelect()->where('main_table.used > 0');
        $this->distinct(true);
        return $this;
    }
}
