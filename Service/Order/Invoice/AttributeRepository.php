<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Order\Invoice;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface;

class AttributeRepository implements \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterfaceFactory
     */
    private $invoiceAttributeFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Order\Invoice\Attribute
     */
    private $invoiceAttributeResource;

    /**
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterfaceFactory $invoiceAttributeFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Order\Invoice\Attribute $invoiceAttributeResource
     */
    public function __construct(
        \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterfaceFactory $invoiceAttributeFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Order\Invoice\Attribute $invoiceAttributeResource
    ) {
        $this->invoiceAttributeFactory = $invoiceAttributeFactory;
        $this->invoiceAttributeResource = $invoiceAttributeResource;
    }

    /**
     * @param mixed[] $data
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     */
    public function getNew(array $data = [])
    {
        return $this->invoiceAttributeFactory->create($data);
    }

    /**
     * @param int $invoiceAttributeId
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($invoiceAttributeId)
    {
        /** @var \Swarming\StoreCredit\Model\Order\Invoice\Attribute $invoiceAttribute */
        $invoiceAttribute = $this->getNew();
        $this->invoiceAttributeResource->load($invoiceAttribute, $invoiceAttributeId);
        if (!$invoiceAttribute->getAttributeId()) {
            throw new NoSuchEntityException(__('Credits invoice attribute with id "%1" does not exist.', $invoiceAttribute));
        }
        return $invoiceAttribute;
    }

    /**
     * @param int $invoiceId
     * @param bool $force
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByInvoiceId($invoiceId, $force = false)
    {
        /** @var \Swarming\StoreCredit\Model\Order\Invoice\Attribute $invoiceAttribute */
        $invoiceAttribute = $this->getNew();
        $this->invoiceAttributeResource->load($invoiceAttribute, $invoiceId, InvoiceAttributeInterface::INVOICE_ID);
        if (!$invoiceAttribute->getAttributeId() && !$force) {
            throw new NoSuchEntityException(__('Credits invoice attribute is not found invoice with id "%1".', $invoiceId));
        }
        return $invoiceAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceAttribute
     * @return \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(InvoiceAttributeInterface $invoiceAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Order\Invoice\Attribute $invoiceAttribute */
            $this->invoiceAttributeResource->save($invoiceAttribute);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save credits invoice attribute: %1', $e->getMessage()));
        }
        return $invoiceAttribute;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceAttribute
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(InvoiceAttributeInterface $invoiceAttribute)
    {
        try {
            /** @var \Swarming\StoreCredit\Model\Order\Invoice\Attribute $invoiceAttribute */
            $this->invoiceAttributeResource->delete($invoiceAttribute);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the credits invoice attribute: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * @param int $invoiceAttributeId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($invoiceAttributeId)
    {
        return $this->delete($this->getById($invoiceAttributeId));
    }
}
