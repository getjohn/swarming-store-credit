<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

class CreditsManagement implements \Swarming\StoreCredit\Api\CreditsManagementInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
    ) {
        $this->configGeneral = $configGeneral;
        $this->cartRepository = $cartRepository;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
    }

    /**
     * @param int $cartId
     * @param float $amount
     * @return void
     */
    public function applyCredits($cartId, $amount)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);

        if (!$this->configGeneral->isActive($quote->getStoreId())) {
            return;
        }

        $this->validateQuote($quote);

        $quoteAttribute = $this->quoteAttributeManagement->getForCart($quote);

        $quoteAttribute->setAvailable($this->filterAmount($amount, $quote->getStoreId()));
        $quoteAttribute->setCredits(0);
        $quoteAttribute->setAmount(0);
        $quoteAttribute->setBaseAmount(0);

        $quote->collectTotals();
        $this->cartRepository->save($quote);
    }

    /**
     * @param float|int $amount
     * @param int $storeId
     * @return float|int
     */
    private function filterAmount($amount, $storeId)
    {
        return $this->configGeneral->isAllowedFractional($storeId) ? $amount : floor($amount);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function validateQuote(\Magento\Quote\Model\Quote $quote)
    {
        if ($quote->getItemsCount() === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Totals calculation is not applicable to empty cart')
            );
        }
    }
}
