<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Sales\Order\Create\Totals;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Credits extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals
{
    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        array $data = []
    ) {
        $this->creditsCurrency = $creditsCurrency;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $salesData, $salesConfig, $data);
    }

    /**
     * @return float
     */
    public function getCredits()
    {
        return $this->getTotal()->getData('credits');
    }

    /**
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->getTotal()->getData('is_formated') === true
            ? $value
            : $this->creditsCurrency->format($this->getCredits(), ConfigDisplay::FORMAT_TOTAL, $this->getStoreId(), $value);
    }
}
