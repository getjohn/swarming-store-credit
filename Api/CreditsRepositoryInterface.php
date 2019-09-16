<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\CreditInterface;

/**
 * @api
 */
interface CreditsRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $creditId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($creditId);

    /**
     * @param int $customerId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credit
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CreditInterface $credit);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credit
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CreditInterface $credit);

    /**
     * @param int $creditId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($creditId);

    /**
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByCustomerId($customerId);
}
