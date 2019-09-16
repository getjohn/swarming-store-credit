<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class Store
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    private $orderFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->orderFactory = $orderFactory;
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $customerId
     * @param int|null $orderId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore($customerId, $orderId = null)
    {
        $store = $orderId ? $this->getStoreIdByOrderId($orderId) : $this->getStoreByCustomer($customerId);
        return $store;
    }

    /**
     * @param int $customerId
     * @param int|null $orderId
     * @return int
     */
    public function getStoreId($customerId, $orderId = null)
    {
        return $this->getStore($customerId, $orderId)->getId();
    }

    /**
     * @param int $orderId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    private function getStoreIdByOrderId($orderId)
    {
        $order = $this->orderFactory->create();
        $order->load($orderId);
        return $this->storeManager->getStore($order->getStoreId());
    }

    /**
     * @param int $customerId
     * @param bool $fromWebsiteOnly
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStoreByCustomer($customerId, $fromWebsiteOnly = false)
    {
        $customer = $this->customerRegistry->retrieve($customerId);
        if ($customer->getStoreId() > 0 && $fromWebsiteOnly === false) {
            $store = $this->storeManager->getStore($customer->getStoreId());
        } elseif ($customer->getWebsiteId() > 0) {
            $store = $this->storeManager->getWebsite($customer->getWebsiteId())->getDefaultStore();
        } else {
            $store = $this->storeManager->getWebsite()->getDefaultStore();
        }
        return $store;
    }
}
