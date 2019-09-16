<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Cron\Credits\Notification;

use Swarming\StoreCredit\Model\ResourceModel\Credit as CreditResourceModel;

class Expiration
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Expiration
     */
    private $configExpiration;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Swarming\StoreCredit\Model\Credits\ExpirationNotifier
     */
    private $creditsExpirationNotifier;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Swarming\StoreCredit\Model\Credits\ExpirationNotifier $creditsExpirationNotifier
     * @param int $pageSize
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Swarming\StoreCredit\Model\Credits\ExpirationNotifier $creditsExpirationNotifier,
        $pageSize = 1000
    ) {
        $this->configExpiration = $configExpiration;
        $this->websiteRepository = $websiteRepository;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->creditsExpirationNotifier = $creditsExpirationNotifier;
        $this->pageSize = (int)$pageSize;
    }

    /**
     * @return void
     */
    public function execute()
    {
        foreach ($this->websiteRepository->getList() as $website) {
            $storeId = $website->getDefaultStore()->getId();
            if (!$this->configExpiration->isActive($storeId)) {
                continue;
            }

            if (!$this->configExpiration->getExpirationReminderDays($storeId)) {
                continue;
            }

            $this->processWebsite($website->getId(), $storeId);
        }
    }

    /**
     * @param int $websiteId
     * @param int $defaultStoreId
     * @return void
     */
    private function processWebsite($websiteId, $defaultStoreId)
    {
        $pageNumber = 1;
        $offset = 0;

        $customerCollection = $this->getCustomerCollection($websiteId);
        do {
            $customerIds = $customerCollection->getAllIds($this->pageSize, $offset);
            if (empty($customerIds)) {
                break;
            }

            $this->creditsExpirationNotifier->notify($customerIds, $defaultStoreId);

            $offset = $pageNumber * $this->pageSize;
            $pageNumber++;
        } while (count($customerIds) == $this->pageSize);
    }

    /**
     * @param int $websiteId
     * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    private function getCustomerCollection($websiteId)
    {
        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addAttributeToFilter('website_id', ['eq' => $websiteId]);
        $customerCollection->joinTable(
            ['credits' => $customerCollection->getTable(CreditResourceModel::TABLE_NAME)],
            'customer_id = entity_id ',
            ['balance'],
            'credits.balance > 0'
        );

        return $customerCollection;
    }
}
