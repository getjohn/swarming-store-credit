<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\OrderAttributeInterface;

/**
 * @api
 */
interface OrderAttributeRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $orderAttributeId
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($orderAttributeId);

    /**
     * @param int $orderId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrderId($orderId, $force = false);

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderAttribute
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderAttributeInterface $orderAttribute);

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OrderAttributeInterface $orderAttribute);

    /**
     * @param int $orderAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($orderAttributeId);
}
