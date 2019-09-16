<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Sales\Order;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Swarming\StoreCredit\Model\Order\Invoice\Total\Credits as InvoiceTotalCredits;
use Swarming\StoreCredit\Model\Order\Creditmemo\Total\Credits as CreditmemoTotalCredits;

class Totals extends \Swarming\StoreCredit\Block\Sales\Order\Totals
{
    /**
     * @return bool|\Magento\Sales\Block\Order\Totals
     */
    protected function getOrderTotals()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock instanceof \Magento\Sales\Block\Adminhtml\Totals) {
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

        $order = $orderTotals->getOrder();
        $this->addOrderTotal($order, $orderTotals);
        $this->addOrderTotalPaid($order, $orderTotals);
        $this->addOrderTotalRefunded($order, $orderTotals);
        $this->addOrderTotalDue($order, $orderTotals);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addOrderTotalPaid($order, $orderTotals)
    {
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCreditsPaid() < 0.01) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => InvoiceTotalCredits::CODE,
                'strong' => true,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($orderCredits->getCreditsPaid(), ConfigDisplay::FORMAT_TOTAL, $order->getStoreId(), $orderCredits->getAmountPaid()),
                'base_value' => $this->priceCurrency->format($orderCredits->getBaseAmountPaid(), false),
                'label' => __('Total %1 Paid', $this->configDisplay->getName($order->getStoreId())),
                'area' => 'footer',
            ]),
            'due'
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addOrderTotalRefunded($order, $orderTotals)
    {
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCreditsRefunded() < 0.01) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => CreditmemoTotalCredits::CODE,
                'strong' => true,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($orderCredits->getCreditsRefunded(), ConfigDisplay::FORMAT_TOTAL, $order->getStoreId(), $orderCredits->getAmountRefunded()),
                'base_value' => $this->priceCurrency->format($orderCredits->getBaseAmountRefunded(), false),
                'label' => __('Total %1 Refunded', $this->configDisplay->getName($order->getStoreId())),
                'area' => 'footer',
            ]),
            InvoiceTotalCredits::CODE
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addOrderTotalDue($order, $orderTotals)
    {
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCredits() < 0.01) {
            return;
        }

        $creditsDue = $orderCredits->getCredits() - $orderCredits->getCreditsPaid();
        $creditsAmountDue = min(
            abs($orderCredits->getAmount()) - abs($orderCredits->getAmountPaid()),
            $this->creditsCurrency->convertCreditsToCurrency($creditsDue, $order->getStoreId())
        );
        $creditsBaseAmountDue = min(
            abs($orderCredits->getBaseAmount()) - abs($orderCredits->getBaseAmountPaid()),
            $this->creditsCurrency->convertCreditsToBase($creditsDue, $order->getStoreId())
        );

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => 'swarming_credits_due',
                'strong' => true,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($creditsDue, ConfigDisplay::FORMAT_TOTAL, $order->getStoreId(), -$creditsAmountDue),
                'base_value' => $this->priceCurrency->format(-$creditsBaseAmountDue, false),
                'label' => __('Total %1 Due', $this->configDisplay->getName($order->getStoreId())),
                'area' => 'footer',
            ]),
            CreditmemoTotalCredits::CODE
        );
    }
}
