<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface;

/**
 * @api
 */
interface CreditmemoAttributeRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $creditmemoAttributeId
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($creditmemoAttributeId);

    /**
     * @param int $creditmemoId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCreditmemoId($creditmemoId, $force = false);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoAttribute
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CreditmemoAttributeInterface $creditmemoAttribute);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CreditmemoAttributeInterface $creditmemoAttribute);

    /**
     * @param int $creditmemoAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($creditmemoAttributeId);
}
