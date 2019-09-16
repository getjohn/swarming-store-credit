<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\LinkInterface;

class LinkRepository implements \Swarming\StoreCredit\Api\LinkRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\LinkInterfaceFactory
     */
    private $linkFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Link
     */
    private $linkResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkInterfaceFactory $linkFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Link $linkResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\LinkInterfaceFactory $linkFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Link $linkResource
    ) {
        $this->linkFactory = $linkFactory;
        $this->linkResource = $linkResource;
    }

    /**
     * @param array $data
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     */
    public function getNew(array $data = [])
    {
        return $this->linkFactory->create($data);
    }

    /**
     * @param int $linkId
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($linkId)
    {
        $link = $this->getNew();
        $this->linkResource->load($link, $linkId);
        if (!$link->getTransactionId()) {
            throw new NoSuchEntityException(__('Transaction link with id "%1" does not exist.', $link));
        }
        return $link;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkInterface $link
     * @return \Swarming\StoreCredit\Api\Data\LinkInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(LinkInterface $link)
    {
        try {
            $this->linkResource->save($link);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save transaction link: %1', $e->getMessage()));
        }
        return $link;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\LinkInterface $link
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(LinkInterface $link)
    {
        try {
            $this->linkResource->delete($link);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the transaction link: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $linkId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($linkId)
    {
        return $this->delete($this->getById($linkId));
    }
}
