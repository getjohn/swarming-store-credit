<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Quote;

use Magento\Quote\Model\Cart\CartTotalRepository;

class TotalDetails
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Swarming\StoreCredit\Api\Data\TotalDetailsInterfaceFactory
     */
    private $detailsFactory;

    /**
     * @var \Magento\Quote\Api\Data\TotalsExtensionFactory
     */
    private $extensionFactory;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteCreditsInterface
     */
    private $quoteCredits;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Swarming\StoreCredit\Api\Data\TotalDetailsInterfaceFactory $detailsFactory
     * @param \Magento\Quote\Api\Data\TotalsExtensionFactory $extensionFactory
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     * @param \Swarming\StoreCredit\Api\QuoteCreditsInterface $creditsSpend
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Swarming\StoreCredit\Api\Data\TotalDetailsInterfaceFactory $detailsFactory,
        \Magento\Quote\Api\Data\TotalsExtensionFactory $extensionFactory,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement,
        \Swarming\StoreCredit\Api\QuoteCreditsInterface $creditsSpend
    ) {
        $this->configGeneral = $configGeneral;
        $this->quoteRepository = $quoteRepository;
        $this->detailsFactory = $detailsFactory;
        $this->extensionFactory = $extensionFactory;
        $this->creditsCurrency = $creditsCurrency;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
        $this->quoteCredits = $creditsSpend;
    }

    /**
     * @param CartTotalRepository $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @return \Magento\Quote\Model\Cart\Totals
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGet(CartTotalRepository $subject, \Closure $proceed, $cartId)
    {
        /** @var \Magento\Quote\Model\Cart\Totals $result */
        $result = $proceed($cartId);

        $cart = $this->quoteRepository->getActive($cartId);

        if ($this->configGeneral->isActive($cart->getStoreId()) && $cart->getCustomerId()) {
            $details = $this->getCreditsDetails($cart);
            $extensionAttributes = $this->getExtensionAttributes($result);
            $extensionAttributes->setCreditsTotalDetails($details);
        }
        return $result;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @return \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
     */
    private function getCreditsDetails($cart)
    {
        $details = $this->detailsFactory->create();

        $details->setMaxAllowedCredits($this->quoteCredits->getMaxAllowed($cart));

        $quoteAttribute = $this->quoteAttributeManagement->getForCart($cart);
        $details->setCredits($this->creditsCurrency->round($quoteAttribute->getCredits(), $cart->getStoreId()));
        $details->setCreditsAmount($quoteAttribute->getAmount());
        $details->setBaseCreditsAmount($quoteAttribute->getBaseAmount());

        return $details;
    }

    /**
     * @param \Magento\Quote\Model\Cart\Totals $cartTotals
     * @return \Magento\Quote\Api\Data\TotalsExtensionInterface
     */
    private function getExtensionAttributes($cartTotals)
    {
        $attributes = $cartTotals->getExtensionAttributes();
        if ($attributes === null) {
            $attributes = $this->extensionFactory->create();
            $cartTotals->setExtensionAttributes($attributes);
        }
        return $attributes;
    }
}
