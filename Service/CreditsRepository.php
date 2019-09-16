<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\CreditInterface;

class CreditsRepository implements \Swarming\StoreCredit\Api\CreditsRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\CreditInterfaceFactory
     */
    private $creditFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Credit
     */
    private $creditResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterfaceFactory $creditFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Credit $creditResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\CreditInterfaceFactory $creditFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Credit $creditResource
    ) {
        $this->creditFactory = $creditFactory;
        $this->creditResource = $creditResource;
    }

    /**
     * @param array $data
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    public function getNew(array $data = [])
    {
        return $this->creditFactory->create($data);
    }

    /**
     * @param int $creditId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($creditId)
    {
        $credit = $this->getNew();
        $this->creditResource->load($credit, $creditId);
        if (!$credit->getCreditId()) {
            throw new NoSuchEntityException(__('Credit with id "%1" does not exist.', $creditId));
        }
        return $credit;
    }

    /**
     * @param int $customerId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        $credit = $this->getNew();
        $this->creditResource->load($credit, $customerId, CreditInterface::CUSTOMER_ID);
        if (!$credit->getCreditId()) {
            throw new NoSuchEntityException(__('Credit for customer id "%1" does not exist.', $customerId));
        }
        return $credit;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credit
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CreditInterface $credit)
    {
        try {
            $this->creditResource->save($credit);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save credit: %1', $e->getMessage()));
        }
        return $credit;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credit
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CreditInterface $credit)
    {
        try {
            $this->creditResource->delete($credit);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the credit: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $creditId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($creditId)
    {
        return $this->delete($this->getById($creditId));
    }

    /**
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteByCustomerId($customerId)
    {
        return $this->delete($this->getByCustomerId($customerId));
    }
}
