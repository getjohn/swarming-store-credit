<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class Currency
{
    const PRECISION = 2;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceFormatter;

    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolver;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        $this->priceFormatter = $priceFormatter;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @param float $credits
     * @param string $formatType
     * @param int|null $storeId
     * @param float|null $currencyAmount
     * @return float
     */
    public function format($credits, $formatType, $storeId = null, $currencyAmount = null)
    {
        $currencyAmount = $currencyAmount
            ? $this->priceFormatter->format($currencyAmount, false)
            : $this->convertCreditsToCurrency($credits, $storeId, true);

        $placeholders = $this->preparePlaceholders($credits, $currencyAmount, $storeId);
        return str_replace(array_keys($placeholders), array_values($placeholders), $this->configDisplay->getFormat($formatType, $storeId));
    }

    /**
     * @param float $credits
     * @param float $currencyAmount
     * @param int|null $storeId
     * @return array
     *
     * @throws \Zend_Locale_Exception
     */
    private function preparePlaceholders($credits, $currencyAmount, $storeId = null)
    {
        return [
            '{{name}}' => $this->configDisplay->getName($storeId),
            '{{icon}}' => $this->configDisplay->getIconHtml($storeId),
            '{{symbol}}' => $this->configDisplay->getSymbol($storeId),
            '{{credits}}' => $this->round($credits, $storeId),
            '{{currency_amount}}' => $currencyAmount
        ];
    }

    /**
     * @param float $credits
     * @param null $storeId
     * @param bool $format
     * @return string
     */
    public function convertCreditsToCurrency($credits, $storeId = null, $format = false)
    {
        $baseAmount = $this->convertCreditsToBase($credits, $storeId);
        return $format
            ? $this->priceFormatter->convertAndFormat($baseAmount, false)
            : $this->priceFormatter->convert($baseAmount);
    }

    /**
     * @param float $credits
     * @param int $storeId
     * @return float
     */
    public function convertCreditsToBase($credits, $storeId = null)
    {
        $rate = $this->configGeneral->getExchangeRate($storeId);
        if ($rate['base'] != $rate['credits']) {
            $credits = $credits * $rate['base'] / $rate['credits'];
        }
        return $credits;
    }

    /**
     * @param float $amount
     * @param int $storeId
     * @param bool $round
     * @return float
     *
     * @throws \Zend_Locale_Exception
     */
    public function convertBaseToCredits($amount, $storeId = null, $round = false)
    {
        $rate = $this->configGeneral->getExchangeRate($storeId);
        if ($rate['base'] != $rate['credits']) {
            $amount = $amount * $rate['credits'] / $rate['base'];
        }
        return $round ? $this->round($amount, $storeId) : $amount;
    }

    /**
     * @param float $credits
     * @param int $storeId
     * @return float
     *
     * @throws \Zend_Locale_Exception
     */
    public function round($credits, $storeId = null)
    {
        $numberFormatter = new \NumberFormatter($this->localeResolver->getLocale(), \NumberFormatter::DECIMAL);
        $decimalSymbol = $numberFormatter->getSymbol(\NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);

        return $this->configGeneral->isAllowedFractional($storeId)
            ? (float)number_format((float)$credits, self::PRECISION, $decimalSymbol, '')
            : ceil($credits);
    }
}
