<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Backend\Quote;

use Magento\Framework\Event\Observer;

class ApplyCredits implements \Magento\Framework\Event\ObserverInterface
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
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
    ) {
        $this->configGeneral = $configGeneral;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\AdminOrder\Create $orderCreateData */
        $orderCreateData = $observer->getData('order_create_model');
        $quote = $orderCreateData->getQuote();

        if (!$this->configGeneral->isActive($quote->getStoreId()) || !$quote->getCustomerId()) {
            return;
        }

        $requestData = $observer->getData('request');
        $amount = $this->getCredits($requestData);
        if (false === $amount) {
            return;
        }

        $quoteAttribute = $this->quoteAttributeManagement->getForCart($quote);

        $quoteAttribute->setAvailable($this->filterAmount($amount, $quote->getStoreId()));
        $quoteAttribute->setCredits(0);
        $quoteAttribute->setAmount(0);
        $quoteAttribute->setBaseAmount(0);
    }

    /**
     * @param array $requestData
     * @return float
     */
    private function getCredits($requestData)
    {
        return isset($requestData['swarming_credits']) ? (float)$requestData['swarming_credits'] : false;
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
}
