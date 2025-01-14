<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\Transaction;

use Swarming\StoreCredit\Api\Data\TransactionInterface;

class Validator
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Source\TransactionTypes
     */
    private $transactionTypes;

    /**
     * @var array
     */
    private $allowedFields = [
        TransactionInterface::TYPE,
        TransactionInterface::AMOUNT,
        TransactionInterface::SUMMARY,
        TransactionInterface::SUPPRESS_NOTIFICATION,
        TransactionInterface::ORDER_ID,
        TransactionInterface::INVOICE_ID,
        TransactionInterface::CREDITMEMO_ID
    ];

    /**
     * @param \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes
    ) {
        $this->transactionTypes = $transactionTypes;
    }

    /**
     * @param array $transactionData
     * @return array
     */
    public function validate(array &$transactionData)
    {
        $messages = [];
        $messages['type'] = $this->validateType($transactionData);
        $messages['amount'] = $this->validateAmount($transactionData);
        $messages['summary'] = $this->validateSummary($transactionData);
        $messages['suppress_notification'] = $this->validateSuppressNotification($transactionData);
        $messages = array_filter($messages); // remove any empty validation results

        $transactionData = $this->filterFields($transactionData);

        return $messages;
    }

    /**
     * @param array $transactionData
     * @return array
     */
    public function filterFields(array $transactionData)
    {
        return array_intersect_key($transactionData, array_flip($this->allowedFields));
    }

    /**
     * @param array $transactionData
     * @return array
     */
    private function validateType(array $transactionData)
    {
        $errors = [];
        if (empty($transactionData[TransactionInterface::TYPE])) {
            $errors[] = __('Action type is not set.');
            return $errors;
        }

        if (!in_array($transactionData[TransactionInterface::TYPE], array_keys($this->transactionTypes->toArray()), true)) {
            $errors[] = __('Action type is unknown.');
        }
        return $errors;
    }

    /**
     * @param array $transactionData
     * @return array
     */
    private function validateAmount(array $transactionData)
    {
        $errors = [];
        if (!isset($transactionData[TransactionInterface::AMOUNT]) || (string)$transactionData[TransactionInterface::AMOUNT] === '') {
            $errors[] = __('Amount is required.');
            return $errors;
        }

        if (!is_numeric($transactionData[TransactionInterface::AMOUNT])) {
            $errors[] = __('Amount must be a number.');
            return $errors;
        }

        if ($transactionData[TransactionInterface::AMOUNT] < 0) {
            $errors[] = __('Amount must be 0 or greater.');
        }

        return $errors;
    }

    /**
     * @param array $transactionData
     * @return array
     */
    private function validateSummary(array &$transactionData)
    {
        if (empty($transactionData[TransactionInterface::SUMMARY])) {
            $transactionData[TransactionInterface::SUMMARY] = __('Manual adjustment.');
        }
        return [];
    }

    /**
     * @param array $transactionData
     * @return array
     */
    private function validateSuppressNotification(array &$transactionData)
    {
        $transactionData[TransactionInterface::SUPPRESS_NOTIFICATION] = isset($transactionData[TransactionInterface::SUPPRESS_NOTIFICATION])
            ? (bool)$transactionData[TransactionInterface::SUPPRESS_NOTIFICATION]
            : false;

        return [];
    }
}
