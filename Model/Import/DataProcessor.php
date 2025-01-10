<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\Import;

use Swarming\StoreCredit\Model\Import\Adapter as ImportAdapter;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\AbstractEntity as ImportAbstractEntity;
use Swarming\StoreCredit\Api\Data\TransactionInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

class DataProcessor
{
    const PATH_SWARMING_CREDITS_IMPORT_UNIQUE_ATTR = 'swarming_credits/import/unique_attribute';
    const PATH_SWARMING_CREDITS_IMPORT_UNIQUE_LABEL = 'swarming_credits/import/unique_label';
    const PATH_SWARMING_CREDITS_IMPORT_CREATE = 'swarming_credits/import/create_customer';
    const PATH_SWARMING_CREDITS_IMPORT_SUPPRESS = 'swarming_credits/import/always_suppress';
    const FAKE_EMAIL_SUFFIX = '@invalid.email.example.com';

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

    protected $scopeConfig;
    protected $customerRepository;
    protected $searchCriteria;
    protected $logger;

    protected $customerUniqueAttrToId = []; // [unique_attr] = ID

    // properties of the current import
    protected $currentWebsiteCode = null; // website code
    protected $currentUniqueAttr = null;
    protected $currentUniqueLabel = null;
    protected $currentCreateCustomer = null;
    protected $alwaysSuppress = null;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerIFactory;

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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria,
        AccountManagementInterface $customerAccountManagement,
        CustomerInterfaceFactory $customerIFactory,
        \Psr\Log\LoggerInterface $logger,
        array $supportedActions = []
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteria = $searchCriteria;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerIFactory = $customerIFactory;
        $this->logger = $logger;

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
        if($this->currentUniqueAttr && !empty($rowData[$this->currentUniqueLabel]) && !empty($this->customerUniqueAttrToId[$rowData[$this->currentUniqueLabel]])) {
            $rowData[TransactionInterface::CUSTOMER_ID] = $this->customerUniqueAttrToId[$rowData[$this->currentUniqueLabel]];
        } else {
            $rowData[TransactionInterface::CUSTOMER_ID] = $this->getCustomerId(
                $rowData[ImportAdapter::COLUMN_EMAIL],
                $rowData[ImportAdapter::COLUMN_WEBSITE]
            );
        }

        $rowData[TransactionInterface::SUMMARY] = !empty($rowData[ImportAdapter::COLUMN_SUMMARY])
            ? $rowData[ImportAdapter::COLUMN_SUMMARY]
            : null;

        $rowData[TransactionInterface::SUPPRESS_NOTIFICATION] = !empty($rowData[ImportAdapter::COLUMN_SUPPRESS_NOTIFICATION])
            ? (bool)$rowData[ImportAdapter::COLUMN_SUPPRESS_NOTIFICATION]
            : (bool)$this->alwaysSuppress;

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

        if (!empty($rowData[ImportAdapter::COLUMN_WEBSITE])) {
            if($this->currentWebsiteCode && $this->currentWebsiteCode != $rowData[ImportAdapter::COLUMN_WEBSITE]) {
                $this->addRowError(ImportAdapter::ERROR_INVALID_WEBSITE, $rowNumber, ImportAdapter::COLUMN_WEBSITE);
                return !$errorAggregator->isRowInvalid($rowNumber);
            } else {
                $this->currentWebsiteCode = $rowData[ImportAdapter::COLUMN_WEBSITE];
            }
        }

        if($this->currentUniqueAttr === null) {
            $this->currentUniqueAttr = $this->scopeConfig->getValue(self::PATH_SWARMING_CREDITS_IMPORT_UNIQUE_ATTR, ScopeInterface::SCOPE_WEBSITE, $this->currentWebsiteCode);
        }
        if($this->currentUniqueLabel === null) {
            $this->currentUniqueLabel = $this->scopeConfig->getValue(self::PATH_SWARMING_CREDITS_IMPORT_UNIQUE_LABEL, ScopeInterface::SCOPE_WEBSITE, $this->currentWebsiteCode) ?: $this->currentUniqueAttr;
        }
        if($this->currentCreateCustomer === null) {
            $this->currentCreateCustomer = (int)$this->scopeConfig->getValue(self::PATH_SWARMING_CREDITS_IMPORT_CREATE, ScopeInterface::SCOPE_WEBSITE, $this->currentWebsiteCode) ?: 0;
        }
        if($this->alwaysSuppress === null) {
            $this->alwaysSuppress = (int)$this->scopeConfig->getValue(self::PATH_SWARMING_CREDITS_IMPORT_SUPPRESS, ScopeInterface::SCOPE_WEBSITE, $this->currentWebsiteCode) ?: 0;
        }

        $customer = null;
        if (empty($rowData[ImportAdapter::COLUMN_EMAIL]) && $this->currentUniqueAttr && !empty($rowData[$this->currentUniqueLabel])) {
            /**
             * Create the customer if they don't exist.  This is really bad practice to do during 'validate', but it seemed more sensible than
             * breaking the requirement for a valid customer email address.  It's idempotent so it should be safe.
             */
            $customer = $this->getCustomerByAttr($this->currentUniqueAttr, $rowData[$this->currentUniqueLabel], $this->currentCreateCustomer, $this->currentWebsiteCode);
            if(!$customer) {
                $this->addRowError(ImportAdapter::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
                $this->logger->error('Failed to create customer during Store Credit import using '.$this->currentUniqueAttr.'='.$rowData[$this->currentUniqueLabel]);
            } else {
                $rowData[ImportAdapter::COLUMN_EMAIL] = $customer->getEmail();
                if(substr($customer->getEmail(), -1 * strlen(self::FAKE_EMAIL_SUFFIX)) === self::FAKE_EMAIL_SUFFIX) { // suppress email to a fake customer
                    $rowData[TransactionInterface::SUPPRESS_NOTIFICATION] = '1';
                }
                $this->customerUniqueAttrToId[$rowData[$this->currentUniqueLabel]] = $customer->getId();
            }
        }

        if ($this->checkUniqueKey($rowData, $rowNumber)) {
            $customerId = $customer ? $customer->getId() : $this->getCustomerId($rowData[ImportAdapter::COLUMN_EMAIL], $rowData[ImportAdapter::COLUMN_WEBSITE]);
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

        $valid = !$errorAggregator->isRowInvalid($rowNumber);
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

        if (!preg_match('/^\S+@[a-z0-9A-Z.-]+[a-z]$/', $email)) { // cheap replacement for Zend_Validate
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

    protected function getCustomerByAttr($uniqueAttr, $uniqueValue, $createCustomer, $websiteCode)
    {
        $customer = null;
        $websiteId = $this->getWebsiteId($websiteCode);

        $this->searchCriteria->addFilter($uniqueAttr, $uniqueValue, 'eq');
        $this->searchCriteria->addFilter('website_id', $websiteId, 'eq');
        $searchCriteria = $this->searchCriteria->create();
        $list = $this->customerRepository->getList($searchCriteria);
        if ($list->getTotalCount() > 0) {
            foreach ($list->getItems() as $item) {
                $customer = $item;
                break;
            }
        }
        if($customer === null) {
            $customerData = ['email' => $uniqueValue . self::FAKE_EMAIL_SUFFIX, 'firstname' => $uniqueValue, 'lastname' => 'unknown'];
            $customerEntity = $this->customerIFactory->create();
            $customerEntity->setEmail($customerData['email']);
            $customerEntity->setFirstname($customerData['firstname']);
            $customerEntity->setLastname($customerData['lastname']);
            $customerEntity->setWebsiteId($websiteId);
            $customerEntity->setCustomAttribute($uniqueAttr, $uniqueValue);
            try {
                $customer = $this->customerAccountManagement->createAccount($customerEntity, null, "saml_sso");
            }
            catch(\Exception $e) {
                $this->logger->error('Failed to create customer '.$customerData['email'].': '.$e->getMessage());
            }
        }
        return $customer; // could be null
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
