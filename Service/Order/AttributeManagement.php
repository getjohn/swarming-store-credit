<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Order;

use Magento\Sales\Api\Data\OrderInterface;

class AttributeManagement implements \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function getForOrder($order)
    {
        $orderAttributes = $order->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(OrderInterface::class);
        $order->setExtensionAttributes($orderAttributes);

        $orderCredits = $orderAttributes->getCredits();
        if (!$orderCredits) {
            $orderCredits = $order->getEntityId()
                ? $this->orderAttributeRepository->getByOrderId($order->getEntityId(), true)
                : $this->orderAttributeRepository->getNew();

            $orderAttributes->setCredits($orderCredits);
        }

        $orderCredits->setOrderId($order->getEntityId());
        return $orderCredits;
    }
}
