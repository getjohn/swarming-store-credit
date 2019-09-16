<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Swarming\StoreCredit\Model\ResourceModel\Credit as ResourceModelCredit;
use Swarming\StoreCredit\Model\ResourceModel\Transaction as ResourceModelTransaction;
use Swarming\StoreCredit\Model\ResourceModel\Link as ResourceModelLink;
use Swarming\StoreCredit\Model\ResourceModel\Quote\Attribute as ResourceModelQuoteAttribute;
use Swarming\StoreCredit\Model\ResourceModel\Order\Attribute as ResourceModelOrderAttribute;
use Swarming\StoreCredit\Model\ResourceModel\Order\Invoice\Attribute as ResourceModelInvoiceAttribute;
use Swarming\StoreCredit\Model\ResourceModel\Order\Creditmemo\Attribute as ResourceModelCreditmemoAttribute;
use Swarming\StoreCredit\Api\Data\CreditInterface;
use Swarming\StoreCredit\Api\Data\TransactionInterface;
use Swarming\StoreCredit\Api\Data\LinkInterface;
use Swarming\StoreCredit\Api\Data\QuoteAttributeInterface;
use Swarming\StoreCredit\Api\Data\OrderAttributeInterface;
use Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface;
use Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->installCreditsTable($setup);

        $this->installTransactionTable($setup);

        $this->installLinkTable($setup);

        $this->installQuoteAttributeTable($setup);

        $this->installOrderAttributeTable($setup);

        $this->installInvoiceAttributeTable($setup);

        $this->installCreditmemoAttributeTable($setup);

        $setup->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installCreditsTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelCredit::TABLE_NAME))
            ->addColumn(
                CreditInterface::CREDIT_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Credit ID'
            )
            ->addColumn(
                CreditInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                CreditInterface::BALANCE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Balance'
            )
            ->addColumn(
                CreditInterface::TOTAL_HELD,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'On Hold'
            )
            ->addColumn(
                CreditInterface::TOTAL_EARNED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Total earned'
            )
            ->addColumn(
                CreditInterface::TOTAL_SPENT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Total spent'
            )
            ->addColumn(
                CreditInterface::LAST_ACTION,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Last Action DateTime'
            )
            ->addIndex(
                $setup->getIdxName(ResourceModelCredit::TABLE_NAME, [CreditInterface::CUSTOMER_ID], AdapterInterface::INDEX_TYPE_UNIQUE),
                [CreditInterface::CUSTOMER_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $setup->getFkName(ResourceModelCredit::TABLE_NAME, CreditInterface::CUSTOMER_ID, 'customer_entity', 'entity_id'),
                CreditInterface::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Credits Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installTransactionTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelTransaction::TABLE_NAME))
            ->addColumn(
                TransactionInterface::TRANSACTION_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Transaction ID'
            )
            ->addColumn(
                TransactionInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                TransactionInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits amount'
            )
            ->addColumn(
                TransactionInterface::BALANCE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Balance at Time of Transaction'
            )
            ->addColumn(
                TransactionInterface::USED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Used amount'
            )
            ->addColumn(
                TransactionInterface::ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Order ID'
            )
            ->addColumn(
                TransactionInterface::INVOICE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Invoice ID'
            )
            ->addColumn(
                TransactionInterface::CREDITMEMO_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Creditmemo ID'
            )
            ->addColumn(
                TransactionInterface::AT_TIME,
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Action DateTime'
            )
            ->addColumn(
                TransactionInterface::SUMMARY,
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Summary'
            )
            ->addColumn(
                TransactionInterface::TYPE,
                Table::TYPE_TEXT,
                10,
                ['nullable' => false],
                'Type'
            )
            ->addIndex($setup->getIdxName(ResourceModelTransaction::TABLE_NAME, [TransactionInterface::CUSTOMER_ID]), [TransactionInterface::CUSTOMER_ID])
            ->addIndex($setup->getIdxName(ResourceModelTransaction::TABLE_NAME, [TransactionInterface::TYPE]), [TransactionInterface::TYPE])
            ->addForeignKey(
                $setup->getFkName(ResourceModelTransaction::TABLE_NAME, TransactionInterface::CUSTOMER_ID, 'customer_entity', 'entity_id'),
                TransactionInterface::CUSTOMER_ID,
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(ResourceModelTransaction::TABLE_NAME, TransactionInterface::ORDER_ID, 'sales_order', 'entity_id'),
                TransactionInterface::ORDER_ID,
                $setup->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(ResourceModelTransaction::TABLE_NAME, TransactionInterface::INVOICE_ID, 'sales_invoice', 'entity_id'),
                TransactionInterface::INVOICE_ID,
                $setup->getTable('sales_invoice'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(ResourceModelTransaction::TABLE_NAME, TransactionInterface::CREDITMEMO_ID, 'sales_creditmemo', 'entity_id'),
                TransactionInterface::CREDITMEMO_ID,
                $setup->getTable('sales_creditmemo'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Transactions Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installLinkTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelLink::TABLE_NAME))
            ->addColumn(
                LinkInterface::LINK_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Link ID'
            )
            ->addColumn(
                LinkInterface::TRANSACTION_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Transaction ID'
            )
            ->addColumn(
                LinkInterface::TRANSACTION_LINK_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Transaction Link ID'
            )
            ->addColumn(
                LinkInterface::ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Order ID'
            )
            ->addColumn(
                LinkInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits amount'
            )
            ->addForeignKey(
                $setup->getFkName(ResourceModelLink::TABLE_NAME, LinkInterface::TRANSACTION_ID, ResourceModelTransaction::TABLE_NAME, TransactionInterface::TRANSACTION_ID),
                LinkInterface::TRANSACTION_ID,
                $setup->getTable(ResourceModelTransaction::TABLE_NAME),
                TransactionInterface::TRANSACTION_ID,
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(ResourceModelLink::TABLE_NAME, LinkInterface::TRANSACTION_LINK_ID, ResourceModelTransaction::TABLE_NAME, TransactionInterface::TRANSACTION_ID),
                LinkInterface::TRANSACTION_LINK_ID,
                $setup->getTable(ResourceModelTransaction::TABLE_NAME),
                TransactionInterface::TRANSACTION_ID,
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Transaction Link Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installQuoteAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelQuoteAttribute::TABLE_NAME))
            ->addColumn(
                QuoteAttributeInterface::ATTRIBUTE_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                QuoteAttributeInterface::QUOTE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Quote ID'
            )
            ->addColumn(
                QuoteAttributeInterface::AVAILABLE,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Available Credits'
            )
            ->addColumn(
                QuoteAttributeInterface::CREDITS,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Amount'
            )
            ->addColumn(
                QuoteAttributeInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Amount'
            )
            ->addColumn(
                QuoteAttributeInterface::BASE_AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Amount'
            )
            ->addIndex($setup->getIdxName(ResourceModelQuoteAttribute::TABLE_NAME, [QuoteAttributeInterface::QUOTE_ID]), [QuoteAttributeInterface::QUOTE_ID])
            ->addForeignKey(
                $setup->getFkName(ResourceModelQuoteAttribute::TABLE_NAME, QuoteAttributeInterface::QUOTE_ID, 'quote', 'entity_id'),
                QuoteAttributeInterface::QUOTE_ID,
                $setup->getTable('quote'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Quote Attribute Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installOrderAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelOrderAttribute::TABLE_NAME))
            ->addColumn(
                OrderAttributeInterface::ATTRIBUTE_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                OrderAttributeInterface::ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order ID'
            )
            ->addColumn(
                OrderAttributeInterface::CREDITS,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Amount'
            )
            ->addColumn(
                OrderAttributeInterface::CREDITS_PAID,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Paid Amount'
            )
            ->addColumn(
                OrderAttributeInterface::CREDITS_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Refund Amount'
            )
            ->addColumn(
                OrderAttributeInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Amount'
            )
            ->addColumn(
                OrderAttributeInterface::AMOUNT_PAID,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Paid Amount'
            )
            ->addColumn(
                OrderAttributeInterface::AMOUNT_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Refund Amount'
            )
            ->addColumn(
                OrderAttributeInterface::BASE_AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Amount'
            )
            ->addColumn(
                OrderAttributeInterface::BASE_AMOUNT_PAID,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Paid Amount'
            )
            ->addColumn(
                OrderAttributeInterface::BASE_AMOUNT_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Refunded Amount'
            )
            ->addIndex($setup->getIdxName(ResourceModelOrderAttribute::TABLE_NAME, [OrderAttributeInterface::ORDER_ID]), [OrderAttributeInterface::ORDER_ID])
            ->addForeignKey(
                $setup->getFkName(ResourceModelOrderAttribute::TABLE_NAME, OrderAttributeInterface::ORDER_ID, 'sales_order', 'entity_id'),
                OrderAttributeInterface::ORDER_ID,
                $setup->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Credits Order Attribute Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installInvoiceAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelInvoiceAttribute::TABLE_NAME))
            ->addColumn(
                InvoiceAttributeInterface::ATTRIBUTE_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                InvoiceAttributeInterface::INVOICE_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Invoice ID'
            )
            ->addColumn(
                InvoiceAttributeInterface::CREDITS,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Amount'
            )
            ->addColumn(
                InvoiceAttributeInterface::CREDITS_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Refunded Amount'
            )
            ->addColumn(
                InvoiceAttributeInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Amount'
            )
            ->addColumn(
                InvoiceAttributeInterface::AMOUNT_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Refunded Amount'
            )
            ->addColumn(
                InvoiceAttributeInterface::BASE_AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Amount'
            )
            ->addColumn(
                InvoiceAttributeInterface::BASE_AMOUNT_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Refunded Amount'
            )
            ->addIndex($setup->getIdxName(ResourceModelInvoiceAttribute::TABLE_NAME, [InvoiceAttributeInterface::INVOICE_ID]), [InvoiceAttributeInterface::INVOICE_ID])
            ->addForeignKey(
                $setup->getFkName(ResourceModelInvoiceAttribute::TABLE_NAME, InvoiceAttributeInterface::INVOICE_ID, 'sales_invoice', 'entity_id'),
                InvoiceAttributeInterface::INVOICE_ID,
                $setup->getTable('sales_invoice'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Credits Invoice Attribute Table');
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @return void
     */
    private function installCreditmemoAttributeTable($setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable(ResourceModelCreditmemoAttribute::TABLE_NAME))
            ->addColumn(
                CreditmemoAttributeInterface::ATTRIBUTE_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false],
                'Attribute ID'
            )
            ->addColumn(
                CreditmemoAttributeInterface::CREDITMEMO_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Creditmemo ID'
            )
            ->addColumn(
                CreditmemoAttributeInterface::CREDITS,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Amount'
            )
            ->addColumn(
                CreditmemoAttributeInterface::CREDITS_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Refund Amount'
            )
            ->addColumn(
                CreditmemoAttributeInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Credits Currency Amount'
            )
            ->addColumn(
                CreditmemoAttributeInterface::AMOUNT_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Currency Refund Amount'
            )
            ->addColumn(
                CreditmemoAttributeInterface::BASE_AMOUNT,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Amount'
            )
            ->addColumn(
                CreditmemoAttributeInterface::BASE_AMOUNT_REFUNDED,
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false, 'default' => '0'],
                'Base Currency Refunded Amount'
            )
            ->addIndex($setup->getIdxName(ResourceModelCreditmemoAttribute::TABLE_NAME, [CreditmemoAttributeInterface::CREDITMEMO_ID]), [CreditmemoAttributeInterface::CREDITMEMO_ID])
            ->addForeignKey(
                $setup->getFkName(ResourceModelCreditmemoAttribute::TABLE_NAME, CreditmemoAttributeInterface::CREDITMEMO_ID, 'sales_creditmemo', 'entity_id'),
                CreditmemoAttributeInterface::CREDITMEMO_ID,
                $setup->getTable('sales_creditmemo'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Swarming Credits Creditmemo Attribute Table');
        $setup->getConnection()->createTable($table);
    }
}
