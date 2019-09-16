<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class Refund
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Refund
     */
    private $configRefund;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
     */
    private $creditmemoAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
    ) {
        $this->configRefund = $configRefund;
        $this->creditsCurrency = $creditsCurrency;
        $this->creditmemoAttributeManagement = $creditmemoAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param bool $isIgnoreConfig
     * @return float
     */
    public function getMaxCreditsForRefund($creditmemo, $isIgnoreConfig = false)
    {
        $creditmemoCredits = $this->creditmemoAttributeManagement->getForCreditmemo($creditmemo);

        $maxCredits = $creditmemoCredits->getCredits();
        if ($this->configRefund->isRefundCurrencyEnabled($creditmemo->getStoreId()) && !$isIgnoreConfig) {
            $maxCredits += $this->creditsCurrency->convertBaseToCredits($creditmemo->getBaseGrandTotal(), $creditmemo->getStoreId(), true);
        }
        return $maxCredits;
    }
}
