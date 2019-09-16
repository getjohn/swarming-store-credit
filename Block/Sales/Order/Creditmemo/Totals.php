<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Sales\Order\Creditmemo;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Swarming\StoreCredit\Model\Quote\Address\Total\Credits as QuoteTotalCredits;
use Swarming\StoreCredit\Model\Order\Creditmemo\Total\Credits as CreditmemoTotalCredits;

class Totals extends \Swarming\StoreCredit\Block\Sales\Order\Totals
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
     */
    protected $creditmemoAttributeManagement;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Magento\Framework\DataObject\Factory $dataObjectFactory
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement,
        array $data = []
    ) {
        $this->creditmemoAttributeManagement = $creditmemoAttributeManagement;
        parent::__construct(
            $context,
            $configDisplay,
            $creditsCurrency,
            $priceCurrency,
            $dataObjectFactory,
            $orderAttributeManagement,
            $data
        );
    }

    /**
     * @return bool|\Magento\Sales\Block\Order\Creditmemo\Totals
     */
    protected function getOrderTotals()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock instanceof \Magento\Sales\Block\Order\Creditmemo\Totals) {
            return $parentBlock;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $orderTotals = $this->getOrderTotals();
        if (!$orderTotals) {
            return $this;
        }

        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $orderTotals->getCreditmemo();
        $this->addCreditmemoTotal($creditmemo, $orderTotals);
        $this->addRefundTotal($creditmemo, $orderTotals);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addCreditmemoTotal($creditmemo, $orderTotals)
    {
        $creditmemoCredits = $this->creditmemoAttributeManagement->getForCreditmemo($creditmemo);
        if ($creditmemoCredits->getCredits() < 0.01) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => QuoteTotalCredits::CODE,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($creditmemoCredits->getCredits(), ConfigDisplay::FORMAT_TOTAL, $creditmemo->getStoreId(), $creditmemoCredits->getAmount()),
                'base_value' => $this->priceCurrency->format($creditmemoCredits->getBaseAmount(), false),
                'label' => $this->configDisplay->getName($creditmemo->getStoreId())
            ])
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addRefundTotal($creditmemo, $orderTotals)
    {
        $creditmemoCredits = $this->creditmemoAttributeManagement->getForCreditmemo($creditmemo);
        if ($creditmemoCredits->getCreditsRefunded() < 0.01) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => CreditmemoTotalCredits::CODE,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($creditmemoCredits->getCreditsRefunded(), ConfigDisplay::FORMAT_TOTAL, $creditmemo->getStoreId(), $creditmemoCredits->getAmountRefunded()),
                'base_value' => $this->priceCurrency->format($creditmemoCredits->getBaseAmountRefunded(), false),
                'label' => __('Total %1 Refunded', $this->configDisplay->getName($creditmemo->getStoreId())),
                'area' => 'footer',
            ]),
            'grand_total'
        );
    }
}
