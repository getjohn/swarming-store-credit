<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Import;

use Swarming\StoreCredit\Api\Data\TransactionInterface;

class Adapter extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const ENTITY_TYPE_CODE = 'swarming_credits';

    /**#@+
     * Column names
     */
    const COLUMN_EMAIL = 'email';
    const COLUMN_WEBSITE = 'website';
    const COLUMN_ACTION = 'action';
    const COLUMN_AMOUNT = 'amount';
    const COLUMN_SUMMARY = 'summary';
    const COLUMN_SUPPRESS_NOTIFICATION = 'suppress_notification';

    /**#@+
     * Error codes
     */
    const ERROR_EMAIL_IS_EMPTY = 'emailIsEmpty';
    const ERROR_INVALID_EMAIL = 'invalidEmail';

    const ERROR_WEBSITE_IS_EMPTY = 'websiteIsEmpty';
    const ERROR_INVALID_WEBSITE = 'invalidWebsite';

    const ERROR_CUSTOMER_NOT_FOUND = 'customerNotFound';

    const ERROR_CREDIT_RECORD_NOT_FOUND = 'creditRecordNotFound';
    const ERROR_CREDIT_BALANCE_NOT_ENOUGH = 'creditBalanceNotEnough';

    const ERROR_AMOUNT_IS_EMPTY = 'amountIsEmpty';
    const ERROR_INVALID_AMOUNT = 'invalidAmount';

    const ERROR_ACTION_IS_EMPTY = 'actionIsEmpty';
    const ERROR_INVALID_ACTION = 'invalidAction';

    const ERROR_MESSAGE_TOO_LONG = 'messageTooLong';

    /**
     * @var \Swarming\StoreCredit\Api\CreditsCustomerInterface
     */
    private $creditsCustomer;

    /**
     * @var \Swarming\StoreCredit\Model\Import\DataProcessor
     */
    private $dataProcessor;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\ImportExport\Helper\Data $importExportData
     * @param \Magento\ImportExport\Model\ResourceModel\Import\Data $importData
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
     * @param \Swarming\StoreCredit\Model\Import\DataProcessor $dataProcessor
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer,
        \Swarming\StoreCredit\Model\Import\DataProcessor $dataProcessor
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
        $this->creditsCustomer = $creditsCustomer;
        $this->dataProcessor = $dataProcessor;

        $this->initErrorMessageTemplates();
    }

    /**
     * @return void
     */
    private function initErrorMessageTemplates()
    {
        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_EMAIL_IS_EMPTY, 'Please specify an email.');
        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_INVALID_EMAIL, 'We found an invalid value in a email column.');
        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_WEBSITE_IS_EMPTY, 'Please specify a website.');
        $this->getErrorAggregator()->addErrorMessageTemplate(
            self::ERROR_INVALID_WEBSITE,
            'We found an invalid value in a website column.'
        );
        $this->getErrorAggregator()->addErrorMessageTemplate(
            self::ERROR_CUSTOMER_NOT_FOUND,
            'We can\'t find a customer who matches this email and website code.'
        );
        $this->getErrorAggregator()->addErrorMessageTemplate(
            self::ERROR_CREDIT_RECORD_NOT_FOUND,
            'Credit record is not created for the customer.'
        );
        $this->getErrorAggregator()->addErrorMessageTemplate(
            self::ERROR_CREDIT_BALANCE_NOT_ENOUGH,
            'Amount is grater than balance of the customer.'
        );
        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_AMOUNT_IS_EMPTY, 'Please specify an amount.');
        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_INVALID_AMOUNT, 'Amount should be positive number greater 0.');
        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_ACTION_IS_EMPTY, 'Please specify an action.');
        $this->getErrorAggregator()->addErrorMessageTemplate(
            self::ERROR_INVALID_ACTION,
            'We found invalid action (supported actions: ' . implode(', ', $this->dataProcessor->getSupportedActions()) . ')'
        );
        $this->getErrorAggregator()->addErrorMessageTemplate(self::ERROR_MESSAGE_TOO_LONG, 'Summary massage too long.');
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * @param array $rowData
     * @param int $rowNumber
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
        }

        $this->_validatedRows[$rowNumber] = true;
        $this->_processedEntitiesCount++;

        $this->dataProcessor->validateRow($rowNumber, $rowData, $this->getErrorAggregator());

        return !$this->getErrorAggregator()->isRowInvalid($rowNumber);
    }

    /**
     * @param array $rowData
     * @return array
     */
    protected function _prepareRowForDb(array $rowData)
    {
        $rowData = parent::_prepareRowForDb($rowData);
        $rowData = $this->dataProcessor->prepareRow($rowData);
        return $rowData;
    }

    /**
     * @return bool
     */
    protected function _importData()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $this->saveBunchCreditTransactions($bunch);
        }
        return true;
    }

    /**
     * @param array $bunch
     * @return void
     */
    private function saveBunchCreditTransactions($bunch)
    {
        foreach ($bunch as $rowNum => $rowData) {
            if (!$this->validateRow($rowData, $rowNum)) {
                continue;
            }

            $adjustmentData = [
                TransactionInterface::TYPE => $rowData[self::COLUMN_ACTION],
                TransactionInterface::AMOUNT => $rowData[self::COLUMN_AMOUNT],
                TransactionInterface::SUMMARY => $rowData[self::COLUMN_SUMMARY],
                TransactionInterface::SUPPRESS_NOTIFICATION => $rowData[self::COLUMN_SUPPRESS_NOTIFICATION]
            ];
            $this->creditsCustomer->update($rowData[TransactionInterface::CUSTOMER_ID], $adjustmentData);
        }
    }
}
