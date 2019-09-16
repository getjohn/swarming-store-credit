<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Creditmemo;

use Magento\Framework\Exception\LocalizedException;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Adjustment
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Swarming\StoreCredit\Helper\Refund
     */
    private $refundHelper;

    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
     */
    private $creditmemoAttributeManagement;

    /**
     * @param ConfigDisplay $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Swarming\StoreCredit\Helper\Refund $refundHelper
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
     */
    public function __construct(
        ConfigDisplay $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Swarming\StoreCredit\Helper\Refund $refundHelper,
        \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
    ) {
        $this->configDisplay = $configDisplay;
        $this->creditsCurrency = $creditsCurrency;
        $this->priceCurrency = $priceCurrency;
        $this->refundHelper = $refundHelper;
        $this->creditmemoAttributeManagement = $creditmemoAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param float $creditsRefund
     * @param bool $isOnline
     * @param bool $validateAndUpdate
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processAdjustment($creditmemo, $creditsRefund, $isOnline, $validateAndUpdate)
    {
        $storeId = $creditmemo->getStoreId();
        $creditsRefund = $this->creditsCurrency->round($creditsRefund, $storeId);

        $creditsRefund = $this->validateAmountRefund($creditmemo, $creditsRefund, $isOnline, $validateAndUpdate);

        $creditmemoCredits = $this->creditmemoAttributeManagement->getForCreditmemo($creditmemo);

        $creditmemoCredits->setCreditsRefunded($creditsRefund);
        $creditmemoCredits->setAmountRefunded($this->creditsCurrency->convertCreditsToCurrency($creditsRefund, $storeId));
        $creditmemoCredits->setBaseAmountRefunded($this->creditsCurrency->convertCreditsToBase($creditsRefund, $storeId));

        if ($validateAndUpdate) {
            $this->updateCreditmemo($creditmemo, $creditmemoCredits);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoCredits
     * @return void
     */
    private function updateCreditmemo($creditmemo, $creditmemoCredits)
    {
        if ($creditmemoCredits->getCredits() < $creditmemoCredits->getCreditsRefunded()) {
            $creditsToCurrencyRefund = $creditmemoCredits->getCreditsRefunded() - $creditmemoCredits->getCredits();
            $this->updateCreditmemoGrandTotal($creditmemo, $creditsToCurrencyRefund);
        }

        if (!$creditmemo->getBaseGrandTotal()) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param float $creditsToCurrencyRefund
     * @return void
     */
    private function updateCreditmemoGrandTotal($creditmemo, $creditsToCurrencyRefund)
    {
        $creditsToCurrencyRefundBaseAmount = $this->creditsCurrency->convertCreditsToBase(
            $creditsToCurrencyRefund,
            $creditmemo->getStoreId()
        );
        $creditsToCurrencyRefundBaseAmount = min($creditsToCurrencyRefundBaseAmount, $creditmemo->getBaseGrandTotal());
        $creditmemoBaseGrandTotal = $this->priceCurrency->round($creditmemo->getBaseGrandTotal() - $creditsToCurrencyRefundBaseAmount);
        $creditmemo->setBaseGrandTotal($creditmemoBaseGrandTotal);


        $creditsToCurrencyRefundAmount = $this->creditsCurrency->convertCreditsToCurrency(
            $creditsToCurrencyRefund,
            $creditmemo->getStoreId()
        );
        $creditsToCurrencyRefundAmount = min($creditsToCurrencyRefundAmount, $creditmemo->getGrandTotal());
        $creditmemoGrandTotal = $this->priceCurrency->round($creditmemo->getGrandTotal() - $creditsToCurrencyRefundAmount);
        $creditmemo->setGrandTotal($creditmemoGrandTotal);
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param float $creditsRefund
     * @param bool $isOnline
     * @param bool $validate
     * @return float
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateAmountRefund($creditmemo, $creditsRefund, $isOnline, $validate)
    {
        if ($validate && $creditsRefund < 0) {
            throw new LocalizedException(
                __('The %1 amount must be positive.', $this->configDisplay->getName($creditmemo->getStoreId()))
            );
        }
        $creditsRefund = max(0, $creditsRefund);

        $maxCredits = $this->refundHelper->getMaxCreditsForRefund($creditmemo, $isOnline);
        if ($validate && $maxCredits < $creditsRefund) {
            throw new LocalizedException(
                __(
                    'The %1 amount must be equal or less than %2.',
                    $this->configDisplay->getName($creditmemo->getStoreId()),
                    $this->creditsCurrency->format($maxCredits, ConfigDisplay::FORMAT_TOTAL, $creditmemo->getStoreId())
                )
            );
        }
        return min($maxCredits, $creditsRefund);
    }
}
