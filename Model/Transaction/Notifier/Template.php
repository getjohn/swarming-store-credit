<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction\Notifier;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Template
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Expiration
     */
    private $configExpiration;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var \Magento\Sales\Api\CreditmemoRepositoryInterface
     */
    private $creditmemoRepository;

    /**
     * @var \Swarming\StoreCredit\Helper\Expiration
     */
    private $creditsExpiration;

    /**
     * @var \Swarming\StoreCredit\Helper\TransactionSummary
     */
    private $transactionSummery;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository
     * @param \Swarming\StoreCredit\Helper\Expiration $creditsExpiration
     * @param \Swarming\StoreCredit\Helper\TransactionSummary $transactionSummery
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Swarming\StoreCredit\Helper\Expiration $creditsExpiration,
        \Swarming\StoreCredit\Helper\TransactionSummary $transactionSummery
    ) {
        $this->configExpiration = $configExpiration;
        $this->configDisplay = $configDisplay;
        $this->creditsCurrency = $creditsCurrency;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->creditsExpiration = $creditsExpiration;
        $this->transactionSummery = $transactionSummery;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store $store
     * @return array
     */
    public function getTemplateVars($transaction, $customer, $store)
    {
        $templateVars = [
            'credits_name' => $this->configDisplay->getName($store->getId()),
            'credits_life_time' => $this->configExpiration->getLifeTime($store->getId()),
            'expiration_date' => $this->creditsExpiration->getExpirationDate('now', $store->getId()),
            'transaction_amount' => $this->creditsCurrency->format($transaction->getAmount(), ConfigDisplay::FORMAT_BASE, $store->getId()),
            'transaction_comment' => $this->transactionSummery->getSummary($transaction),
            'order_id' => $this->getIncrementOrderId($transaction->getOrderId()),
            'invoice_id' => $this->getIncrementInvoiceId($transaction->getInvoiceId()),
            'creditmemo_id' => $this->getIncrementCreditmemoId($transaction->getCreditmemoId()),
            'transaction' => $transaction,
            'credits_balance' => $this->creditsCurrency->format($transaction->getBalance(), ConfigDisplay::FORMAT_BASE, $store->getId()),
            'customer' => $customer,
            'store' => $store,
            'website_name' => $store->getWebsite()->getName()
        ];

        return $templateVars;
    }

    /**
     * @param int $orderId
     * @return string
     */
    private function getIncrementOrderId($orderId)
    {
        return $orderId ? $this->orderRepository->get($orderId)->getIncrementId() : '';
    }

    /**
     * @param int $invoiceId
     * @return string
     */
    private function getIncrementInvoiceId($invoiceId)
    {
        return $invoiceId ? $this->invoiceRepository->get($invoiceId)->getIncrementId() : '';
    }

    /**
     * @param int $creditmemoId
     * @return string
     */
    private function getIncrementCreditmemoId($creditmemoId)
    {
        return $creditmemoId ? $this->creditmemoRepository->get($creditmemoId)->getIncrementId() : '';
    }
}
