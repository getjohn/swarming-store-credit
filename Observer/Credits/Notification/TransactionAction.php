<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Credits\Notification;

use Magento\Framework\Event\Observer;

class TransactionAction implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Notification
     */
    private $configNotification;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @var \Swarming\StoreCredit\Model\Transaction\Notifier
     */
    private $transactionNotifier;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Notification $configNotification
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param \Swarming\StoreCredit\Model\Transaction\Notifier $transactionNotifier
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Notification $configNotification,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        \Swarming\StoreCredit\Model\Transaction\Notifier $transactionNotifier,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configNotification = $configNotification;
        $this->storeHelper = $storeHelper;
        $this->transactionNotifier = $transactionNotifier;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction */
        $transaction = $observer->getData('transaction');
        $store = $this->storeHelper->getStore($transaction->getCustomerId(), $transaction->getOrderId());

        if (!$this->configNotification->isActive($store->getId())) {
            return;
        }

        if ($transaction->getSuppressNotification()
            || !$this->configNotification->isTransactionNotify($transaction->getType(), $store->getId())
        ) {
            return;
        }

        try {
            $this->transactionNotifier->notify($transaction);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
