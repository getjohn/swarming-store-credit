<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

class OrderRepository
{
    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
    ) {
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->orderAttributeManagement = $orderAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Sales\Api\Data\OrderInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(OrderRepositoryInterface $subject, \Closure $proceed, OrderInterface $order)
    {
        $orderExtension = $order->getExtensionAttributes();

        $proceed($order);

        if ($orderExtension && $orderExtension->getCredits()) {
            $orderCredits = $orderExtension->getCredits();
            $orderCredits->setOrderId($order->getEntityId());
            $this->orderAttributeRepository->save($orderCredits);
        }

        return $order;
    }

    /**
     * Add "credits" extension attributes
     *
     * @param OrderRepositoryInterface $subject
     * @param \Magento\Sales\Model\Order $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, \Magento\Sales\Model\Order $order)
    {
        $this->orderAttributeManagement->getForOrder($order);
        return $order;
    }

    /**
     * Add "credits" extension attributes
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();
        foreach ($orders as &$order) {
            // @var \Magento\Sales\Model\Order $order
            $this->orderAttributeManagement->getForOrder($order);
        }
        return $searchResult;
    }

}
