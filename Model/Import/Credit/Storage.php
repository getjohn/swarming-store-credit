<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Import\Credit;

class Storage
{
    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory
     */
    private $creditsCollectionFactory;

    /**
     * @var \Magento\ImportExport\Model\ResourceModel\CollectionByPagesIterator
     */
    private $byPagesIterator;

    /**
     * @var bool
     */
    private $isCollectionLoaded = false;

    /**
     * @var int
     */
    private $pageSize = 0;

    /**
     * @var int[]
     */
    private $customerIds = [];

    /**
     * @param \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory $creditsCollectionFactory
     * @param \Magento\ImportExport\Model\ResourceModel\CollectionByPagesIterator $byPagesIterator
     * @param array $data
     */
    public function __construct(
        \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory $creditsCollectionFactory,
        \Magento\ImportExport\Model\ResourceModel\CollectionByPagesIterator $byPagesIterator,
        array $data = []
    ) {
        $this->creditsCollectionFactory = $creditsCollectionFactory;
        $this->byPagesIterator = $byPagesIterator;
        $this->pageSize = $data['page_size'] ?? 0;
    }

    /**
     * @return void
     */
    private function load()
    {
        if ($this->isCollectionLoaded == false) {
            $collection = $this->creditsCollectionFactory->create();
            $this->byPagesIterator->iterate(
                $collection,
                $this->pageSize,
                [[$this, 'addCreditRecord']]
            );

            $this->isCollectionLoaded = true;
        }
    }

    /**
     * @param \Swarming\StoreCredit\Model\Credit|\Magento\Framework\DataObject $credit
     * @return $this
     */
    public function addCreditRecord(\Magento\Framework\DataObject $credit)
    {
        $this->customerIds[$credit->getCustomerId()] = $credit->getBalance();

        return $this;
    }

    /**
     * @param int $customerId
     * @return int|bool
     */
    public function getCustomerBalance($customerId)
    {
        $this->load(); // lazy loading

        return $this->customerIds[$customerId] ?? false;
    }
}
