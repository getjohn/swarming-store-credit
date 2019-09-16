<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\TransactionInterface;

class TransactionRepository implements \Swarming\StoreCredit\Api\TransactionRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\TransactionInterfaceFactory
     */
    private $transactionFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Transaction
     */
    private $transactionResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterfaceFactory $transactionFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction $transactionResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\TransactionInterfaceFactory $transactionFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Transaction $transactionResource
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->transactionResource = $transactionResource;
    }

    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function getNew(array $data = [])
    {
        return $this->transactionFactory->create($data);
    }

    /**
     * @param int $transactionId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($transactionId)
    {
        $transaction = $this->getNew();
        $this->transactionResource->load($transaction, $transactionId);
        if (!$transaction->getTransactionId()) {
            throw new NoSuchEntityException(__('Transaction with id "%1" does not exist.', $transaction));
        }
        return $transaction;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(TransactionInterface $transaction)
    {
        try {
            $this->transactionResource->save($transaction);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save transaction: %1', $e->getMessage()));
        }
        return $transaction;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(TransactionInterface $transaction)
    {
        try {
            $this->transactionResource->delete($transaction);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the transaction: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $transactionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($transactionId)
    {
        return $this->delete($this->getById($transactionId));
    }
}
