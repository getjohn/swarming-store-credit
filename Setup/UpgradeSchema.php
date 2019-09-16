<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Swarming\StoreCredit\Model\ResourceModel\Credit as ResourceModelCredit;
use Swarming\StoreCredit\Api\Data\CreditInterface;
use Swarming\StoreCredit\Model\ResourceModel\Transaction as ResourceModelTransaction;
use Swarming\StoreCredit\Api\Data\TransactionInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->addTotalPendingFieldToCreditTable($setup);
            $this->addEmailSendingFieldToTransactionTable($setup);
        }
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return $this
     */
    private function addTotalPendingFieldToCreditTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(ResourceModelCredit::TABLE_NAME),
            CreditInterface::TOTAL_PENDING,
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0',
                'comment' => 'Total pending',
                'after' => CreditInterface::BALANCE
            ]
        );
        return $this;
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return $this
     */
    private function addEmailSendingFieldToTransactionTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(ResourceModelTransaction::TABLE_NAME),
            TransactionInterface::SUPPRESS_NOTIFICATION,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => '0',
                'comment' => 'Suppress Notification',
                'after' => TransactionInterface::CREDITMEMO_ID
            ]
        );
        return $this;
    }
}
