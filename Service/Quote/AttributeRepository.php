<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Quote;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\QuoteAttributeInterface;

class AttributeRepository implements \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\QuoteAttributeInterfaceFactory
     */
    private $quoteAttributeFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Quote\Attribute
     */
    private $quoteAttributeResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterfaceFactory $quoteAttributeFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Quote\Attribute $quoteAttributeResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\QuoteAttributeInterfaceFactory $quoteAttributeFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Quote\Attribute $quoteAttributeResource
    ) {
        $this->quoteAttributeFactory = $quoteAttributeFactory;
        $this->quoteAttributeResource = $quoteAttributeResource;
    }

    /**
     * @param array $data
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function getNew(array $data = [])
    {
        return $this->quoteAttributeFactory->create($data);
    }

    /**
     * @param int $quoteAttributeId
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($quoteAttributeId)
    {
        /** @var \Swarming\StoreCredit\Model\Quote\Attribute $quoteAttribute */
        $quoteAttribute = $this->getNew();
        $this->quoteAttributeResource->load($quoteAttribute, $quoteAttributeId);
        if (!$quoteAttribute->getAttributeId()) {
            throw new NoSuchEntityException(__('Credits quote attribute with id "%1" does not exist.', $quoteAttribute));
        }
        return $quoteAttribute;
    }

    /**
     * @param int $quoteId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteId($quoteId, $force = false)
    {
        /** @var \Swarming\StoreCredit\Model\Quote\Attribute $quoteAttribute */
        $quoteAttribute = $this->getNew();
        $this->quoteAttributeResource->load($quoteAttribute, $quoteId, QuoteAttributeInterface::QUOTE_ID);
        if (!$quoteAttribute->getAttributeId() && !$force) {
            throw new NoSuchEntityException(__('Credits quote attribute is not found for quote with id "%1".', $quoteId));
        }
        return $quoteAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface $quoteAttribute
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(QuoteAttributeInterface $quoteAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Quote\Attribute $quoteAttribute */
            $this->quoteAttributeResource->save($quoteAttribute);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save credits quote attribute: %1', $e->getMessage()));
        }
        return $quoteAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface $quoteAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(QuoteAttributeInterface $quoteAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Quote\Attribute $quoteAttribute */
            $this->quoteAttributeResource->delete($quoteAttribute);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the credits quote attribute: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $quoteAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($quoteAttributeId)
    {
        return $this->delete($this->getById($quoteAttributeId));
    }

    /**
     * @param int $quoteId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByQuoteId($quoteId)
    {
        $quoteAttribute = $this->getByQuoteId($quoteId, true);
        if ($quoteAttribute->getAttributeId()) {
            $this->delete($quoteAttribute);
        }
    }
}
