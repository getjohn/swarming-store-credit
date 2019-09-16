<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Quote\Address\Total;

class Credits extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    const CODE = 'swarming_credits';

    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    private $priceCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteCreditsInterface
     */
    private $quoteCredits;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Swarming\StoreCredit\Api\QuoteCreditsInterface $quoteCredits
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Swarming\StoreCredit\Api\QuoteCreditsInterface $quoteCredits,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        $this->creditsCurrency = $creditsCurrency;
        $this->priceCurrency = $priceCurrency;
        $this->quoteCredits = $quoteCredits;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
        $this->setCode(self::CODE);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $storeId = $quote->getStoreId();

        if (!$this->configGeneral->isActive($storeId) || !$quote->getCustomerId()) {
            return $this;
        }

        $quoteAttribute = $this->quoteAttributeManagement->getForCart($quote);

        $creditsAvailable = $quoteAttribute->getAvailable();
        $maxAllowedCredits = $this->quoteCredits->getMaxAllowed($quote, $total->getBaseGrandTotal());

        $canUseCredits = min($creditsAvailable, $maxAllowedCredits);
        $creditsBaseAvailable = $this->creditsCurrency->convertCreditsToBase($canUseCredits, $storeId);

        $creditsBaseAmount = min($total->getBaseGrandTotal(), $creditsBaseAvailable);
        $creditsAmount = $this->priceCurrency->convert($creditsBaseAmount, $storeId);
        $credits = $this->creditsCurrency->convertBaseToCredits($creditsBaseAmount, $storeId);

        $creditsAvailable -= $credits;

        $total->setData(self::CODE, $total->getData(self::CODE) + $credits);
        $this->_addAmount(-$creditsAmount);
        $this->_addBaseAmount(-$creditsBaseAmount);

        $total->setGrandTotal($total->getGrandTotal() - $creditsAmount);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $creditsBaseAmount);

        $quoteAttribute->setAvailable($creditsAvailable);
        $quoteAttribute->setCredits($quoteAttribute->getCredits() + $credits);
        $quoteAttribute->setAmount($quoteAttribute->getAmount() + -$creditsAmount);
        $quoteAttribute->setBaseAmount($quoteAttribute->getBaseAmount() + -$creditsBaseAmount);
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $quoteAttribute = $this->quoteAttributeManagement->getForCart($quote);
        return $quoteAttribute->getAmount() < -0.01
            ? $this->getCreditsTotal($quoteAttribute, $quote->getStoreId())
            : null;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface $quoteAttribute
     * @param int $storeId
     * @return array
     */
    private function getCreditsTotal($quoteAttribute, $storeId)
    {
        return [
            'code' => $this->getCode(),
            'title' => $this->configDisplay->getName($storeId),
            'value' => $quoteAttribute->getAmount()
        ];
    }
}
