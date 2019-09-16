<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface;

/**
 * @api
 */
interface InvoiceAttributeRepositoryInterface
{
    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     */
    public function getNew(array $data = []);

    /**
     * @param int $invoiceAttributeId
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($invoiceAttributeId);

    /**
     * @param int $invoiceId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByInvoiceId($invoiceId, $force = false);

    /**
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceAttribute
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(InvoiceAttributeInterface $invoiceAttribute);

    /**
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(InvoiceAttributeInterface $invoiceAttribute);

    /**
     * @param int $invoiceAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($invoiceAttributeId);
}
