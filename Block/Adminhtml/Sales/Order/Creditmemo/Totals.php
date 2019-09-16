<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Sales\Order\Creditmemo;

class Totals extends \Swarming\StoreCredit\Block\Sales\Order\Creditmemo\Totals
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
}
