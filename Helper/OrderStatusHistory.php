<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class OrderStatusHistory
{
    /**
     * @var \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory
     */
    private $orderStatusHistoryFactory;

    /**
     * @var \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface
     */
    private $orderStatusHistoryRepository;

    /**
     * @param \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory
     * @param \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderStatusHistoryInterfaceFactory $orderStatusHistoryFactory,
        \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface $orderStatusHistoryRepository
    ) {
        $this->orderStatusHistoryFactory = $orderStatusHistoryFactory;
        $this->orderStatusHistoryRepository = $orderStatusHistoryRepository;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     * @param string $comment
     * @return void
     */
    public function addComment($order, $comment)
    {
        if ($order->getEntityId()) {
            $orderHistory = $this->orderStatusHistoryFactory->create();

            $orderHistory->setParentId($order->getEntityId());
            $orderHistory->setEntityName($order->getEntityType());
            $orderHistory->setStatus($order->getStatus());
            $orderHistory->setComment($comment);

            $this->orderStatusHistoryRepository->save($orderHistory);
        } else {
            $order->addStatusHistoryComment($comment);
        }
    }
}
