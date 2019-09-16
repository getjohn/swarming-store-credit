<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Swarming\StoreCredit\Api\Data\TransactionInterface;
use Swarming\StoreCredit\Model\Config\Source\ExpirationRepeats;

class TransactionManager implements \Swarming\StoreCredit\Api\TransactionManagerInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Transaction
     */
    private $resourceModelTransaction;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Expiration
     */
    private $configExpiration;

    /**
     * @var \Swarming\StoreCredit\Helper\Transaction
     */
    private $transactionHelper;

    /**
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction $resourceModelTransaction
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param \Swarming\StoreCredit\Helper\Transaction $transactionHelper
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     */
    public function __construct(
        \Swarming\StoreCredit\Model\ResourceModel\Transaction $resourceModelTransaction,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        \Swarming\StoreCredit\Helper\Transaction $transactionHelper,
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
    ) {
        $this->resourceModelTransaction = $resourceModelTransaction;
        $this->storeHelper = $storeHelper;
        $this->transactionHelper = $transactionHelper;
        $this->configExpiration = $configExpiration;
    }

    /**
     * @param int $customerId
     * @return float
     */
    public function getBalance($customerId)
    {
        $select = $this->getSelect();
        $select->from($this->getTransactionTable(), 'SUM(amount - used)');
        $select->where('customer_id = ?', $customerId);
        $select->where('type in (?)', $this->transactionHelper->getGainTypes());

        $lifeTime = $this->getLifeTime($customerId);
        if ($lifeTime > 0) {
            $select->where("DATE(`at_time`) > DATE_SUB(DATE(now()), INTERVAL {$lifeTime} DAY)");
        }
        return (float)$this->getConnection()->fetchOne($select);
    }

    /**
     * @param int $customerId
     * @return int
     */
    private function getLifeTime($customerId)
    {
        $store = $this->storeHelper->getStoreByCustomer($customerId, true);
        return $this->configExpiration->getLifeTime($store->getId());
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getTotalSpent($customerId)
    {
        $bind = ['customer_id' => $customerId, 'type' => TransactionInterface::TYPE_SPEND];
        return (float)$this->getConnection()->fetchOne($this->getSelectTotal('SUM(amount - used)'), $bind);
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getTotalHeld($customerId)
    {
        $bind = ['customer_id' => $customerId, 'type' => TransactionInterface::TYPE_HOLD];
        return (float)$this->getConnection()->fetchOne($this->getSelectTotal('SUM(amount - used)'), $bind);
    }

    /**
     * @param string $sum
     * @return \Magento\Framework\DB\Select
     */
    private function getSelectTotal($sum)
    {
        $select = $this->getSelect();
        $select->from($this->getTransactionTable(), $sum);
        $select->where('customer_id = :customer_id');
        $select->where('type = :type');
        return $select;
    }

    /**
     * @param int[] $customerIds
     * @param int $lifeTime
     * @param int $expirationReminderDays
     * @param string $expirationRepeats
     * @return array
     */
    public function getCustomersExpirationAmounts($customerIds, $lifeTime, $expirationReminderDays, $expirationRepeats)
    {
        $select = $this->getSelect();
        $select->from($this->getTransactionTable(), ['customer_id', 'date' => 'DATE(`at_time`)', 'amount' => 'SUM(`amount` - `used`)']);
        $select->where('customer_id in (?)', $customerIds);
        $select->where('type in (?)', $this->transactionHelper->getGainTypes());

        $notifyPeriod = $lifeTime - $expirationReminderDays;

        if ($expirationRepeats == ExpirationRepeats::ONCE) {
            $select->where("DATE(now()) = DATE_ADD(DATE(`at_time`), INTERVAL {$notifyPeriod} DAY)");
        } else {
            $deyBeforeLifeEnd = $lifeTime - 1;
            $select->where(
                "DATE(now()) BETWEEN DATE_ADD(DATE(`at_time`), INTERVAL {$notifyPeriod} DAY) AND DATE_ADD(DATE(`at_time`), INTERVAL {$deyBeforeLifeEnd} DAY)"
            );
        }
        $select->group(['customer_id', 'DATE(`at_time`)']);
        $select->having('amount > 0');

        $expirationData = $this->getConnection()->fetchAll($select);

        return $this->groupByCustomer($expirationData);
    }

    /**
     * @param array $expirationData
     * @return array
     */
    private function groupByCustomer($expirationData)
    {
        $result = [];
        foreach ($expirationData as $rowData) {
            $result[$rowData['customer_id']][] = ['date' => $rowData['date'], 'amount' => $rowData['amount']];
        }
        return $result;
    }

    /**
     * @return string
     */
    private function getTransactionTable()
    {
        return $this->resourceModelTransaction->getMainTable();
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    private function getSelect()
    {
        return $this->getConnection()->select();
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getConnection()
    {
        return $this->resourceModelTransaction->getConnection();
    }
}
