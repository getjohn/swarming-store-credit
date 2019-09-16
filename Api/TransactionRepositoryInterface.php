<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\TransactionInterface;

/**
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $transactionId
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($transactionId);

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return \Swarming\StoreCredit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(TransactionInterface $transaction);

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(TransactionInterface $transaction);

    /**
     * @param int $transactionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($transactionId);
}
