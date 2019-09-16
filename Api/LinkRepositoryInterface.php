<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\LinkInterface;

/**
 * @api
 */
interface LinkRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $linkId
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($linkId);

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkInterface $link
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(LinkInterface $link);

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkInterface $link
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(LinkInterface $link);

    /**
     * @param int $linkId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($linkId);
}
