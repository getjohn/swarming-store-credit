<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order;

class PlaceAfter implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Swarming\StoreCredit\Model\Order\Relation\PlaceProcessor
     */
    private $orderRelationPlaceProcessor;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Model\Order\Relation\PlaceProcessor $orderRelationPlaceProcessor
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Model\Order\Relation\PlaceProcessor $orderRelationPlaceProcessor
    ) {
        $this->configGeneral = $configGeneral;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->orderRelationPlaceProcessor = $orderRelationPlaceProcessor;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData('order');

        if (!empty($order->getOrigData())) { // Only on the first saving, after placement
            return;
        }

        if (!$this->configGeneral->isActive($order->getStoreId()) || !$order->getCustomerId()) {
            return;
        }

        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCredits() < 0.01) {
            return;
        }

        $this->orderRelationPlaceProcessor->process($order, $orderCredits);
    }
}
