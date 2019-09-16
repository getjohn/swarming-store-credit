<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Quote;

class ConvertToOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
    ) {
        $this->configGeneral = $configGeneral;
        $this->creditsCurrency = $creditsCurrency;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getData('quote');

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');

        if (!$this->configGeneral->isActive($quote->getStoreId()) || !$quote->getCustomerId()) {
            return;
        }

        $quoteCredits = $this->quoteAttributeManagement->getForCart($quote);
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);

        $orderCredits->setCredits($this->creditsCurrency->round($quoteCredits->getCredits(), $order->getStoreId()));
        $orderCredits->setAmount($quoteCredits->getAmount());
        $orderCredits->setBaseAmount($quoteCredits->getBaseAmount());
    }
}
