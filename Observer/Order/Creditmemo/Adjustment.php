<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order\Creditmemo;

class Adjustment implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Refund
     */
    private $configRefund;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Swarming\StoreCredit\Model\Order\Creditmemo\Adjustment
     */
    private $creditmemoAdjustment;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Swarming\StoreCredit\Model\Order\Creditmemo\Adjustment $creditmemoAdjustment
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Magento\Framework\App\Request\Http $request,
        \Swarming\StoreCredit\Model\Order\Creditmemo\Adjustment $creditmemoAdjustment
    ) {
        $this->configRefund = $configRefund;
        $this->request = $request;
        $this->creditmemoAdjustment = $creditmemoAdjustment;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getData('creditmemo');
        $storeId = $creditmemo->getStoreId();

        if (!$this->configRefund->isActive($storeId)
            || !$this->configRefund->isRefundEnabled($storeId)
            || !$creditmemo->getOrder()->getCustomerId()
        ) {
            return;
        }

        $inputData = $observer->getData('input');
        if (empty($inputData)) {
            return;
        }

        $validateAndUpdate = $this->getFullActionName() !== 'sales_order_creditmemo_updateqty';
        $isOnline = isset($inputData['do_offline']) && $validateAndUpdate ? !$inputData['do_offline'] : false;
        $creditsRefund = isset($inputData['swarming_credits_refund']) ? (double)$inputData['swarming_credits_refund'] : 0;

        $this->creditmemoAdjustment->processAdjustment($creditmemo, $creditsRefund, $isOnline, $validateAndUpdate);
    }

    /**
     * @return string
     */
    private function getFullActionName()
    {
        return strtolower($this->request->getFullActionName());
    }
}
