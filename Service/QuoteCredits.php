<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Swarming\StoreCredit\Model\Config\Source\SpendingLimit;

class QuoteCredits implements \Swarming\StoreCredit\Api\QuoteCreditsInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Spending
     */
    private $configSpending;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
     */
    private $quoteAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Spending $configSpending
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Spending $configSpending,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface $quoteAttributeManagement
    ) {
        $this->configSpending = $configSpending;
        $this->creditsCurrency = $creditsCurrency;
        $this->quoteAttributeManagement = $quoteAttributeManagement;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote $cart
     * @param float $originGrandTotal
     * @return float
     */
    public function getMaxAllowed($cart, $originGrandTotal = null)
    {
        $amountOff = $originGrandTotal === null ? $this->getAmountOff($cart) : $originGrandTotal;
        return $this->calculateMaxAllowed($amountOff, $cart->getStoreId(), $cart);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote $cart
     * @return float
     */
    private function getAmountOff($cart)
    {
        $quoteAttribute = $this->quoteAttributeManagement->getForCart($cart);
        return $cart->getBaseGrandTotal() + abs($quoteAttribute->getBaseAmount());
    }

    /**
     * @param float $amountOff
     * @param int $storeId
     * @return float
     */
    private function calculateMaxAllowed($amountOff, $storeId, $cart = null)
    {
        $maxAllowedBasAmount = $this->configSpending->isEnabledLimit($storeId)
            ? $this->calculateLimitation($amountOff, $storeId, $cart)
            : $amountOff;

        $maxAllowedBasAmount = $this->creditsCurrency->convertBaseToCredits($maxAllowedBasAmount, $storeId);
        return $this->creditsCurrency->round($maxAllowedBasAmount, $storeId);
    }

    /**
     * @param float $amountOff
     * @param int $storeId
     * @return float
     */
    private function calculateLimitation($amountOff, $storeId, $cart = null)
    {
        $limitType = $this->configSpending->getLimitType($storeId);
        switch ($limitType) {
            case SpendingLimit::PERCENT:
                $maxAllowedBasAmount = $this->getPercentageMax($amountOff, $storeId, $cart);
                break;
            case SpendingLimit::FIXED:
                $maxAllowedBasAmount = $this->getFixedMax($amountOff, $storeId, $cart);
                break;
            default:
                throw new \DomainException("'{$limitType}' - wrong limit type.");
                break;
        }

        return $maxAllowedBasAmount;
    }

    /**
     * @param float $amountOff
     * @param int $storeId
     * @return float
     */
    private function getPercentageMax($amountOff, $storeId, $cart)
    {
        $customerGroupId = 0;
        if($cart && $cart->getCustomer()) {
            $customerGroupId = $cart->getCustomer()->getGroupId();
        }
        $spendMaxPercentage = $this->configSpending->getPercentageLimit($storeId, $customerGroupId);
        return $spendMaxPercentage ? $amountOff * $spendMaxPercentage/100 : $amountOff;
    }

    /**
     * @param float $amountOff
     * @param int $storeId
     * @return float
     */
    private function getFixedMax($amountOff, $storeId)
    {
        $spendMaxAmount = (float)$this->configSpending->getFixedLimit($storeId);
        return $spendMaxAmount >= 0.01 ? min($spendMaxAmount, $amountOff) : $amountOff;
    }
}
