<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Sales\Order\Create;

use Magento\Framework\Exception\NoSuchEntityException;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Credits extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
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
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    private $customerCredits;

    /**
     * @var \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    private $quoteCredits;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        $this->creditRepository = $creditRepository;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
        $this->creditsCurrency = $creditsCurrency;

        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return !$this->getQuote()->getCustomerIsGuest()
            && $this->configGeneral->isActive($this->getQuote()->getStoreId())
            && (bool)$this->getCustomerCredits()
            && $this->getCreditsBalance() > 0
            && ($this->getQuote()->getBaseGrandTotal() > 0 || $this->getQuoteCreditsAttribute()->getCredits() > 0);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTitle()
    {
        return $this->configDisplay->getBlockTitle($this->getQuote()->getStoreId());
    }

    /**
     * @return float
     */
    public function getCreditsBalance()
    {
        return $this->getCustomerCredits()->getBalance();
    }

    /**
     * @return float
     */
    public function getQuoteCredits()
    {
        return $this->creditsCurrency->round($this->getQuoteCreditsAttribute()->getCredits(), $this->getQuote()->getStoreId());
    }

    /**
     * @return float
     */
    public function getQuoteMaxAmount()
    {
        $maxAmount = $this->creditsCurrency->convertBaseToCredits($this->getQuote()->getBaseGrandTotal(), $this->getQuote()->getStoreId())
            + $this->getQuoteCreditsAttribute()->getCredits();
        $maxAmount = min($this->getCreditsBalance(), $maxAmount);
        return $this->creditsCurrency->round($maxAmount, $this->getQuote()->getStoreId());
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface|bool
     */
    private function getCustomerCredits()
    {
        if (null === $this->customerCredits) {
            $this->customerCredits = $this->fetchCustomerCredits();
        }
        return $this->customerCredits;
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface|bool
     */
    private function fetchCustomerCredits()
    {
        try {
            $credits = $this->creditRepository->getByCustomerId($this->getQuote()->getCustomerId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return $credits;
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    private function getQuoteCreditsAttribute()
    {
        if (null === $this->quoteCredits) {
            $this->quoteCredits = $this->quoteAttributeManagement->getForCart($this->getQuote());
        }
        return $this->quoteCredits;
    }

    /**
     * @param float $amount
     * @return float
     */
    public function formatCurrency($amount)
    {
        return $this->creditsCurrency->format($amount, ConfigDisplay::FORMAT_BASE, $this->getStoreId());
    }
}
