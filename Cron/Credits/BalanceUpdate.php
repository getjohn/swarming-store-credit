<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Cron\Credits;

class BalanceUpdate
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory
     */
    private $creditsCollectionFactory;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsAccountantInterface
     */
    private $accountant;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory $creditsCollectionFactory
     * @param \Swarming\StoreCredit\Api\CreditsAccountantInterface $accountant
     * @param \Psr\Log\LoggerInterface $logger
     * @param int $pageSize
     */
    public function __construct(
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory $creditsCollectionFactory,
        \Swarming\StoreCredit\Api\CreditsAccountantInterface $accountant,
        \Psr\Log\LoggerInterface $logger,
        $pageSize = 1000
    ) {
        $this->creditRepository = $creditRepository;
        $this->creditsCollectionFactory = $creditsCollectionFactory;
        $this->accountant = $accountant;
        $this->logger = $logger;
        $this->pageSize = (int)$pageSize;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $creditsCollection = $this->creditsCollectionFactory->create();
        $creditsCollection->addFieldToFilter('balance', ['gt' => 0]);
        $creditsCollection->setPageSize($this->pageSize);

        $page = 1;
        do {
            $creditsCollection->setCurPage($page);
            $creditsCollection->load();

            foreach ($creditsCollection as $credits) {
                try {
                    $this->accountant->recalculateBalance($credits);
                    $this->creditRepository->save($credits);
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }

            $creditsCollection->clear();
            $page++;
        } while ($creditsCollection->getCurPage() < $creditsCollection->getLastPageNumber());
    }
}
