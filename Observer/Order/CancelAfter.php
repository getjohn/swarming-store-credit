<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order;

class CancelAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Model\Order\Relation\CancelProcessor
     */
    private $orderRelationCancelProcessor;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Model\Order\Relation\CancelProcessor $orderRelationCancelProcessor
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Model\Order\Relation\CancelProcessor $orderRelationCancelProcessor
    ) {
        $this->configGeneral = $configGeneral;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->orderRelationCancelProcessor = $orderRelationCancelProcessor;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');

        if (!$this->configGeneral->isActive($order->getStoreId()) || !$order->getCustomerId()) {
            return;
        }

        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCredits() < 0.01 || $orderCredits->getCredits() <= $orderCredits->getCreditsPaid()) {
            return;
        }

        $this->orderRelationCancelProcessor->process($order, $orderCredits);
    }
}
