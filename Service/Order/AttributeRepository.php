<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Order;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\OrderAttributeInterface;

class AttributeRepository implements \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\OrderAttributeInterfaceFactory
     */
    private $orderAttributeFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Order\Attribute
     */
    private $orderAttributeResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterfaceFactory $orderAttributeFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Order\Attribute $orderAttributeResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\OrderAttributeInterfaceFactory $orderAttributeFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Order\Attribute $orderAttributeResource
    ) {
        $this->orderAttributeFactory = $orderAttributeFactory;
        $this->orderAttributeResource = $orderAttributeResource;
    }

    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function getNew(array $data = [])
    {
        return $this->orderAttributeFactory->create($data);
    }

    /**
     * @param int $orderAttributeId
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($orderAttributeId)
    {
        /** @var \Swarming\StoreCredit\Model\Order\Attribute $orderAttribute */
        $orderAttribute = $this->getNew();
        $this->orderAttributeResource->load($orderAttribute, $orderAttributeId);
        if (!$orderAttribute->getAttributeId()) {
            throw new NoSuchEntityException(__('Credits order attribute with id "%1" does not exist.', $orderAttribute));
        }
        return $orderAttribute;
    }

    /**
     * @param int $orderId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrderId($orderId, $force = false)
    {
        /** @var \Swarming\StoreCredit\Model\Order\Attribute $orderAttribute */
        $orderAttribute = $this->getNew();
        $this->orderAttributeResource->load($orderAttribute, $orderId, OrderAttributeInterface::ORDER_ID);
        if (!$orderAttribute->getAttributeId() && !$force) {
            throw new NoSuchEntityException(__('Credits order attribute is not found for order with id "%1".', $orderId));
        }
        return $orderAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderAttribute
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(OrderAttributeInterface $orderAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Order\Attribute $orderAttribute */
            $this->orderAttributeResource->save($orderAttribute);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save credits order attribute: %1', $e->getMessage()));
        }
        return $orderAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(OrderAttributeInterface $orderAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Order\Attribute $orderAttribute */
            $this->orderAttributeResource->delete($orderAttribute);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the credits order attribute: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $orderAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($orderAttributeId)
    {
        return $this->delete($this->getById($orderAttributeId));
    }
}
