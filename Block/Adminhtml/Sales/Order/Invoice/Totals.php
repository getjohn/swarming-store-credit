<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Sales\Order\Invoice;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Swarming\StoreCredit\Model\Order\Creditmemo\Total\Credits as CreditmemoTotalCredits;

class Totals extends \Swarming\StoreCredit\Block\Sales\Order\Invoice\Totals
{
    /**
     * @return bool|\Magento\Sales\Block\Adminhtml\Order\Invoice\Totals
     */
    protected function getOrderTotals()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals) {
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

        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $orderTotals->getInvoice();
        $this->addInvoiceTotal($invoice, $orderTotals);
        $this->addInvoiceTotalRefunded($invoice, $orderTotals);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addInvoiceTotalRefunded($invoice, $orderTotals)
    {
        $invoiceCredits = $this->invoiceAttributeManagement->getForInvoice($invoice);
        if ($invoiceCredits->getCreditsRefunded() < 0.01) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => CreditmemoTotalCredits::CODE,
                'strong' => true,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($invoiceCredits->getCreditsRefunded(), ConfigDisplay::FORMAT_TOTAL, $invoice->getStoreId(), $invoiceCredits->getAmountRefunded()),
                'base_value' => $this->priceCurrency->format($invoiceCredits->getBaseAmountRefunded(), false),
                'label' => __('Total %1 Refunded', $this->configDisplay->getName($invoice->getStoreId())),
                'area' => 'footer',
            ]),
            'grand_total'
        );
    }
}
