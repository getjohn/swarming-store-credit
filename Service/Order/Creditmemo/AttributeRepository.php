<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Order\Creditmemo;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface;

class AttributeRepository implements \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterfaceFactory
     */
    private $creditmemoAttributeFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Order\Creditmemo\Attribute
     */
    private $creditmemoAttributeResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterfaceFactory $creditmemoAttributeFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Order\Creditmemo\Attribute $creditmemoAttributeResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterfaceFactory $creditmemoAttributeFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Order\Creditmemo\Attribute $creditmemoAttributeResource
    ) {
        $this->creditmemoAttributeFactory = $creditmemoAttributeFactory;
        $this->creditmemoAttributeResource = $creditmemoAttributeResource;
    }

    /**
     * @param array $data
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function getNew(array $data = [])
    {
        return $this->creditmemoAttributeFactory->create($data);
    }

    /**
     * @param int $creditmemoAttributeId
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($creditmemoAttributeId)
    {
        /** @var \Swarming\StoreCredit\Model\Order\Creditmemo\Attribute $creditmemoAttribute */
        $creditmemoAttribute = $this->getNew();
        $this->creditmemoAttributeResource->load($creditmemoAttribute, $creditmemoAttributeId);
        if (!$creditmemoAttribute->getAttributeId()) {
            throw new NoSuchEntityException(__('Credits creditmemo attribute with id "%1" does not exist.', $creditmemoAttribute));
        }
        return $creditmemoAttribute;
    }

    /**
     * @param int $creditmemoId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCreditmemoId($creditmemoId, $force = false)
    {
        /** @var \Swarming\StoreCredit\Model\Order\Creditmemo\Attribute $creditmemoAttribute */
        $creditmemoAttribute = $this->getNew();
        $this->creditmemoAttributeResource->load($creditmemoAttribute, $creditmemoId, CreditmemoAttributeInterface::CREDITMEMO_ID);
        if (!$creditmemoAttribute->getAttributeId() && !$force) {
            throw new NoSuchEntityException(__('Credits creditmemo attribute is not found for creditmemo with id "%1".', $creditmemoId));
        }
        return $creditmemoAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoAttribute
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CreditmemoAttributeInterface $creditmemoAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Order\Creditmemo\Attribute $creditmemoAttribute */
            $this->creditmemoAttributeResource->save($creditmemoAttribute);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save credits creditmemo attribute: %1', $e->getMessage()));
        }
        return $creditmemoAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CreditmemoAttributeInterface $creditmemoAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Order\Creditmemo\Attribute $creditmemoAttribute */
            $this->creditmemoAttributeResource->delete($creditmemoAttribute);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the credits creditmemo attribute: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $creditmemoAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($creditmemoAttributeId)
    {
        return $this->delete($this->getById($creditmemoAttributeId));
    }
}
