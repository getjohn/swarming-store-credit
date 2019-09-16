<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Credits;

use Magento\Framework\Exception\NoSuchEntityException;

class Repairer
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param int $pageSize
     */
    public function __construct(
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        $pageSize = 1000
    ) {
        $this->creditRepository = $creditRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->pageSize = (int)$pageSize;
    }

    /**
     * @param \Closure|null $callback
     * @return int
     */
    public function repair(\Closure $callback = null)
    {
        $repairedItems = 0;

        $pageNumber = 1;
        $offset = 0;

        $customerCollection = $this->customerCollectionFactory->create();
        do {
            $customerIds = $customerCollection->getAllIds($this->pageSize, $offset);
            foreach ($customerIds as $customerId) {
                if (is_callable($callback)) {
                    $callback();
                }

                try {
                    $this->creditRepository->getByCustomerId($customerId);
                    continue;
                } catch (NoSuchEntityException $e) {
                    $repairedItems++;
                    $credits = $this->creditRepository->getNew();
                }

                $credits->setCustomerId($customerId);
                $this->creditRepository->save($credits);
            }

            $offset = $pageNumber * $this->pageSize;
            $pageNumber++;
        } while (count($customerIds) == $this->pageSize);

        return $repairedItems;
    }
}
