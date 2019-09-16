<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Payment;

class AddCreditsSubtotal implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $sessionQuote;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface
     */
    private $quoteAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface $quoteAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface $quoteAttributeRepository
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        $this->checkoutSession = $checkoutSession;
        $this->sessionQuote = $sessionQuote;
        $this->quoteAttributeRepository = $quoteAttributeRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $this->getCurrentStoreId();
        if (!$this->configGeneral->isActive($storeId)) {
            return;
        }

        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getData('cart');

        $quoteId = $cart->getSalesModel()->getDataUsingMethod('id');
        $quoteAttribute = $this->quoteAttributeRepository->getByQuoteId($quoteId, true);

        if (abs($quoteAttribute->getBaseAmount()) > 0) {
            $cart->addCustomItem($this->configDisplay->getName($storeId), 1, $quoteAttribute->getBaseAmount());
        }
    }

    /**
     * @return int|null
     */
    private function getCurrentStoreId()
    {
        if (!$this->checkoutSession->getQuote()->isObjectNew()) {
            return $this->checkoutSession->getQuote()->getStore()->getId();
        } elseif (!$this->sessionQuote->getQuote()->isObjectNew()) {
            return $this->sessionQuote->getQuote()->getStore()->getId();
        }
        return null;
    }
}
