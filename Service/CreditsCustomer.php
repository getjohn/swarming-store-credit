<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

class CreditsCustomer implements \Swarming\StoreCredit\Api\CreditsCustomerInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\TransactionCustomerInterface
     */
    private $transactionCustomer;

    /**
     * @var \Swarming\StoreCredit\Api\TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var \Swarming\StoreCredit\Model\Transaction\Validator
     */
    private $validator;

    /**
     * @param \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Swarming\StoreCredit\Api\TransactionCustomerInterface $transactionCustomer
     * @param \Swarming\StoreCredit\Model\Transaction\Validator $validator
     */
    public function __construct(
        \Swarming\StoreCredit\Api\TransactionCustomerInterface $transactionCustomer,
        \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository,
        \Swarming\StoreCredit\Model\Transaction\Validator $validator
    ) {
        $this->transactionCustomer = $transactionCustomer;
        $this->transactionRepository = $transactionRepository;
        $this->validator = $validator;
    }

    /**
     * @param int $customerId
     * @param mixed[] $adjustmentData
     * @return int
     * @throws \Exception
     * @throws \Magento\Framework\Validator\Exception
     */
    public function update($customerId, array $adjustmentData)
    {
        $this->validateData($adjustmentData);

        $transaction = $this->transactionRepository->getNew(['data' => $adjustmentData]);
        $this->transactionCustomer->addTransaction($customerId, $transaction);
        return $transaction->getTransactionId();
    }

    /**
     * @param int[] $customersId
     * @param mixed[] $adjustmentData
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Validator\Exception
     */
    public function massUpdate(array $customersId, array $adjustmentData)
    {
        $this->validateData($adjustmentData);

        foreach ($customersId as $customerId) {
            $transaction = $this->transactionRepository->getNew(['data' => $adjustmentData]);
            $this->transactionCustomer->addTransaction($customerId, $transaction);
        }
    }

    /**
     * @param mixed[] $transactionData
     * @return void
     * @throws \Magento\Framework\Validator\Exception
     */
    private function validateData(array &$transactionData)
    {
        $errorMessages = $this->validator->validate($transactionData);
        if (!empty($errorMessages)) {
            $validatorException = new \Magento\Framework\Validator\Exception(null, null);
            foreach ($errorMessages as $message) {
                $validatorException->addMessage(new \Magento\Framework\Message\Error($message));
            }
            throw $validatorException;
        }
    }
}
