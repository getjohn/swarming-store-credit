<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;

class OrderRepository
{
    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
    ) {
        $this->orderAttributeRepository = $orderAttributeRepository;
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
}
