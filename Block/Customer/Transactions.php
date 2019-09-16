<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Customer;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Swarming\StoreCredit\Api\Data\TransactionInterface;

class Transactions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var \Swarming\StoreCredit\Helper\TransactionAmountInfo
     */
    private $transactionAmountInfo;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Helper\TransactionSummary
     */
    private $transactionSummery;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Transaction\Collection
     */
    private $transactions;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Swarming\StoreCredit\Helper\TransactionAmountInfo $transactionAmountInfo
     * @param \Swarming\StoreCredit\Helper\TransactionSummary $transactionSummery
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Swarming\StoreCredit\Helper\TransactionAmountInfo $transactionAmountInfo,
        \Swarming\StoreCredit\Helper\TransactionSummary $transactionSummery,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        array $data = []
    ) {
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->transactionAmountInfo = $transactionAmountInfo;
        $this->customerSession = $customerSession;
        $this->creditsCurrency = $creditsCurrency;
        $this->transactionSummery = $transactionSummery;
        $this->storeHelper = $storeHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getTransactions()) {
            /** @var \Magento\Theme\Block\Html\Pager $pager */
            $pager = $this->getChildBlock('pager');
            $pager->setCollection($this->getTransactions());
        }
        return $this;
    }

    /**
     * @return \Swarming\StoreCredit\Model\ResourceModel\Transaction\Collection
     */
    private function prepareTransactionCollection()
    {
        $collection = $this->transactionCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $this->customerSession->getCustomerId())
            ->setOrder('transaction_id', 'desc');
        return $collection;
    }

    /**
     * @return \Swarming\StoreCredit\Model\ResourceModel\Transaction\Collection
     */
    public function getTransactions()
    {
        if (null === $this->transactions) {
            $this->transactions = $this->prepareTransactionCollection();
        }
        return $this->transactions;
    }


    /**
     * @param float $amount
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return float
     */
    public function formatCreditsGrid($amount, TransactionInterface $transaction)
    {
        $storeId = $this->storeHelper->getStoreId($transaction->getCustomerId(), $transaction->getOrderId());
        return $this->creditsCurrency->format($amount, ConfigDisplay::FORMAT_GRID, $storeId);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return \Magento\Framework\Phrase|string
     */
    public function getAmountInfo(TransactionInterface $transaction)
    {
        $storeId = $this->storeHelper->getStoreId($transaction->getCustomerId(), $transaction->getOrderId());
        return $this->transactionAmountInfo->getMessage(
            $transaction->getType(),
            $transaction->getUsed(),
            $transaction->getAmount(),
            $transaction->getAtTime(),
            $storeId
        );
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return \Magento\Framework\Phrase|string
     */
    public function getSummary(TransactionInterface $transaction)
    {
        return $this->transactionSummery->getSummary($transaction);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
