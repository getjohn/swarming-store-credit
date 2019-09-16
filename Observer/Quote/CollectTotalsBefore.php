<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Quote;

class CollectTotalsBefore implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditsRepository;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditsRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditsRepository
    ) {
        $this->configGeneral = $configGeneral;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
        $this->creditsRepository = $creditsRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');

        if (!$this->configGeneral->isActive($quote->getStoreId()) || !$quote->getCustomerId()) {
            return;
        }

        $credits = $this->creditsRepository->getByCustomerId($quote->getCustomerId());
        $quoteAttribute = $this->quoteAttributeManagement->getForCart($quote);

        $availableCredits = $this->calculateAvailableCredits($credits, $quoteAttribute, $quote->getStoreId());
        $quoteAttribute->setAvailable($availableCredits);
        $quoteAttribute->setCredits(0);
        $quoteAttribute->setAmount(0);
        $quoteAttribute->setBaseAmount(0);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface $quoteAttribute
     * @param int $storeId
     * @return int|float
     */
    private function calculateAvailableCredits($credits, $quoteAttribute, $storeId)
    {
        $quoteAvailable = $quoteAttribute->getAvailable() + $quoteAttribute->getCredits();
        $balanceAvailable = $this->calculateBalanceAvailable($credits->getBalance(), $storeId);
        return min($quoteAvailable, $balanceAvailable);
    }

    /**
     * @param float|int $balance
     * @param int $storeId
     * @return float|int
     */
    private function calculateBalanceAvailable($balance, $storeId)
    {
        return $this->configGeneral->isAllowedFractional($storeId) ? $balance : floor($balance);
    }
}
