<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales\Order\Invoice;

use Swarming\StoreCredit\Plugin\Sales\Order\Creditmemo\EditQty as CreditmemoEditQty;

class EditQty extends CreditmemoEditQty
{
    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items $subject
     * @param bool $canCapture
     * @return bool
     */
    public function afterCanCapture($subject, $canCapture)
    {
        if ($canCapture && $subject->getInvoice() && $subject->getInvoice()->getBaseGrandTotal() <= 0) {
            $canCapture = false;
        }
        return $canCapture;
    }
}
