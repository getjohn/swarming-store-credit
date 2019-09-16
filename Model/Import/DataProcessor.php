<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Import;

use Swarming\StoreCredit\Model\Import\Adapter as ImportAdapter;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\AbstractEntity as ImportAbstractEntity;
use Swarming\StoreCredit\Api\Data\TransactionInterface;

class DataProcessor
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\Storage
     */
    private $customerStorage;

    /**
     * @var \Swarming\StoreCredit\Model\Import\Credit\Storage
     */
    private $creditStorage;

    /**
     * @var array
     */
    private $supportedActions = [
        TransactionInterface::TYPE_ADD,
        TransactionInterface::TYPE_SUBTRACT
    ];

    /**
     * @var \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface
     */
    private $errorAggregator;

    /**
     * @var array
     */
    private $websiteCodeToId = [];

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $customerStorageFactory
     * @param \Swarming\StoreCredit\Model\Import\Credit\StorageFactory $creditStorageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $supportedActions
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $customerStorageFactory,
        \Swarming\StoreCredit\Model\Import\Credit\StorageFactory $creditStorageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $supportedActions = []
    ) {
        $this->storeManager = $storeManager;

        $pageSize = (int)$scopeConfig->getValue(ImportAbstractEntity::XML_PATH_PAGE_SIZE) ?: 0;
        $this->customerStorage = $customerStorageFactory->create(['data' => ['page_size' => $pageSize]]);

        $this->creditStorage = $creditStorageFactory->create();

        $this->supportedActions = array_merge($this->supportedActions, array_values($supportedActions));

        $this->initWebsites();
    }

    /**
     * @return string[]
     */
    public function getSupportedActions()
    {
        return $this->supportedActions;
    }

    /**
     * @param array $rowData
     * @return array
     */
    public function prepareRow(array $rowData)
    {
        $rowData[TransactionInterface::CUSTOMER_ID] = $this->getCustomerId(
            $rowData[ImportAdapter::COLUMN_EMAIL],
            $rowData[ImportAdapter::COLUMN_WEBSITE]
        );

        $rowData[TransactionInterface::SUMMARY] = !empty($rowData[ImportAdapter::COLUMN_SUMMARY])
            ? $rowData[ImportAdapter::COLUMN_SUMMARY]
            : null;

        $rowData[TransactionInterface::SUPPRESS_NOTIFICATION] = !empty($rowData[ImportAdapter::COLUMN_SUPPRESS_NOTIFICATION])
            ? (bool)$rowData[ImportAdapter::COLUMN_SUPPRESS_NOTIFICATION]
            : false;

        return $rowData;
    }

    /**
     * @param int $rowNumber
     * @param array $rowData
     * @param \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator
     * @return bool
     */
    public function validateRow($rowNumber, array $rowData, $errorAggregator)
    {
        $this->errorAggregator = $errorAggregator;

        if ($this->checkUniqueKey($rowData, $rowNumber)) {
            $customerId = $this->getCustomerId($rowData[ImportAdapter::COLUMN_EMAIL], $rowData[ImportAdapter::COLUMN_WEBSITE]);
            if ($customerId === false) {
                $this->addRowError(ImportAdapter::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            } elseif ($this->creditStorage->getCustomerBalance($customerId) === false) {
                $this->addRowError(ImportAdapter::ERROR_CREDIT_RECORD_NOT_FOUND, $rowNumber);
            } elseif ($this->isNotEnoughBalance($rowData, $customerId)) {
                $this->addRowError(ImportAdapter::ERROR_CREDIT_BALANCE_NOT_ENOUGH, $rowNumber);
            }

            if (empty($rowData[ImportAdapter::COLUMN_ACTION])) {
                $this->addRowError(ImportAdapter::ERROR_ACTION_IS_EMPTY, $rowNumber);
            } elseif (!in_array($rowData[ImportAdapter::COLUMN_ACTION], $this->supportedActions, true)) {
                $this->addRowError(ImportAdapter::ERROR_INVALID_ACTION, $rowNumber);
            }

            if (empty($rowData[ImportAdapter::COLUMN_AMOUNT])) {
                $this->addRowError(ImportAdapter::ERROR_AMOUNT_IS_EMPTY, $rowNumber);
            } elseif (!is_numeric($rowData[ImportAdapter::COLUMN_AMOUNT]) || $rowData[ImportAdapter::COLUMN_AMOUNT] <= 0) {
                $this->addRowError(ImportAdapter::ERROR_INVALID_AMOUNT, $rowNumber);
            }

            if (!empty($rowData[ImportAdapter::COLUMN_SUMMARY])
                && mb_strlen($rowData[ImportAdapter::COLUMN_SUMMARY]) > ImportAbstractEntity::DB_MAX_TEXT_LENGTH
            ) {
                $this->addRowError(ImportAdapter::ERROR_MESSAGE_TOO_LONG, $rowNumber);
            }
        }

        return !$errorAggregator->isRowInvalid($rowNumber);
    }

    /**
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     */
    private function checkUniqueKey(array $rowData, $rowNumber)
    {
        if (empty($rowData[ImportAdapter::COLUMN_WEBSITE])) {
            $this->addRowError(ImportAdapter::ERROR_WEBSITE_IS_EMPTY, $rowNumber, ImportAdapter::COLUMN_WEBSITE);
        } elseif (empty($rowData[ImportAdapter::COLUMN_EMAIL])) {
            $this->addRowError(ImportAdapter::ERROR_EMAIL_IS_EMPTY, $rowNumber, ImportAdapter::COLUMN_EMAIL);
        } else {
            $email = strtolower($rowData[ImportAdapter::COLUMN_EMAIL]);
            $website = $rowData[ImportAdapter::COLUMN_WEBSITE];

            if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
                $this->addRowError(ImportAdapter::ERROR_INVALID_EMAIL, $rowNumber, ImportAdapter::COLUMN_EMAIL);
            } elseif (!isset($this->websiteCodeToId[$website])) {
                $this->addRowError(ImportAdapter::ERROR_INVALID_WEBSITE, $rowNumber, ImportAdapter::COLUMN_WEBSITE);
            }
        }
        return !$this->errorAggregator->isRowInvalid($rowNumber);
    }

    /**
     * @param $rowData
     * @param $customerId
     * @return bool
     */
    private function isNotEnoughBalance($rowData, $customerId)
    {
        return isset($rowData[ImportAdapter::COLUMN_ACTION])
            && isset($rowData[ImportAdapter::COLUMN_AMOUNT])
            && is_numeric($rowData[ImportAdapter::COLUMN_AMOUNT])
            && $rowData[ImportAdapter::COLUMN_ACTION] === TransactionInterface::TYPE_SUBTRACT
            && $rowData[ImportAdapter::COLUMN_AMOUNT] > $this->creditStorage->getCustomerBalance($customerId);
    }

    /**
     * @return $this
     */
    private function initWebsites()
    {
        /** @var $website \Magento\Store\Model\Website */
        foreach ($this->storeManager->getWebsites(true) as $website) {
            $this->websiteCodeToId[$website->getCode()] = $website->getId();
        }
        return $this;
    }

    /**
     * @param string $websiteCode
     * @return int|false
     */
    private function getWebsiteId($websiteCode)
    {
        return $this->websiteCodeToId[$websiteCode] ?? false;
    }

    /**
     * Get customer id if customer is present in database
     *
     * @param string $email
     * @param string $websiteCode
     * @return bool|int
     */
    private function getCustomerId($email, $websiteCode)
    {
        $email = strtolower(trim($email));
        $websiteId = $this->getWebsiteId($websiteCode);
        return $email && $websiteId
            ? $this->customerStorage->getCustomerId($email, $websiteId)
            : false;
    }

    /**
     * Add error with corresponding current data source row number.
     *
     * @param string $errorCode Error code or simply column name
     * @param int $errorRowNum Row number.
     * @param string $colName OPTIONAL Column name.
     * @param string $errorMessage OPTIONAL Column name.
     * @param string $errorLevel
     * @param string $errorDescription
     * @return $this
     */
    private function addRowError(
        $errorCode,
        $errorRowNum,
        $colName = null,
        $errorMessage = null,
        $errorLevel = ProcessingError::ERROR_LEVEL_CRITICAL,
        $errorDescription = null
    ) {
        $errorCode = (string)$errorCode;
        $this->errorAggregator->addError(
            $errorCode,
            $errorLevel,
            $errorRowNum,
            $colName,
            $errorMessage,
            $errorDescription
        );
        return $this;
    }
}
