<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\QuoteAttributeInterface;

/**
 * @api
 */
interface QuoteAttributeRepositoryInterface
{
    /**
     * @param array $data
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $quoteAttributeId
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($quoteAttributeId);

    /**
     * @param int $quoteId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteId($quoteId, $force = false);

    /**
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface $quoteAttribute
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(QuoteAttributeInterface $quoteAttribute);

    /**
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface $quoteAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(QuoteAttributeInterface $quoteAttribute);

    /**
     * @param int $quoteAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($quoteAttributeId);

    /**
     * @param int $quoteId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteByQuoteId($quoteId);
}
