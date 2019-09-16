<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction;

abstract class ActionAbstract implements ActionInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    protected $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Expiration
     */
    protected $configExpiration;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    protected $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Api\TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory
     */
    protected $transactionCollectionFactory;

    /**
     * @var \Swarming\StoreCredit\Api\LinkRepositoryInterface
     */
    protected $linkRepository;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    protected $storeHelper;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory
     * @param \Swarming\StoreCredit\Api\LinkRepositoryInterface $linkRepository
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository,
        \Swarming\StoreCredit\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        \Swarming\StoreCredit\Api\LinkRepositoryInterface $linkRepository,
        \Swarming\StoreCredit\Helper\Store $storeHelper
    ) {
        $this->configGeneral = $configGeneral;
        $this->configExpiration = $configExpiration;
        $this->configDisplay = $configDisplay;
        $this->transactionRepository = $transactionRepository;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->linkRepository = $linkRepository;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     */
    protected function checkCreditsLimit($credits, $transaction)
    {
        $balance = $credits->getBalance() + $transaction->getAmount();
        $storeId = $this->storeHelper->getStoreId($credits->getCustomerId(), $transaction->getOrderId());
        $maxAmount = $this->configGeneral->getMaxAmount($storeId);

        if ($maxAmount <= 0 || $balance <= $maxAmount) {
            return $this;
        }

        $transactionAmount = $maxAmount - $credits->getBalance();

        if ($transactionAmount == 0) {
            $transactionSummery = __(
                'You have reached your %1 limit which is %2.',
                $this->configDisplay->getName($storeId),
                $maxAmount
            );
        } else {
            $transactionSummery = __(
                'You have gained only %1 %2 because you have reached your %2 limit which is %3.',
                $transactionAmount,
                $this->configDisplay->getName($storeId),
                $maxAmount
            );
        }

        $transaction->setAmount($transactionAmount);
        $transaction->addSummary($transactionSummery);

        return $this;
    }

    /**
     * @param int $transactionId
     * @param int|null $transactionLinkId
     * @param int $use
     * @param int|null $orderId
     * @return $this
     */
    protected function addLink($transactionId, $transactionLinkId, $use, $orderId = null)
    {
        $link = $this->linkRepository->getNew();
        $link->setTransactionId($transactionId);
        $link->setTransactionLinkId($transactionLinkId);
        $link->setAmount($use);
        $link->setOrderId($orderId);
        $this->linkRepository->save($link);

        return $this;
    }

    /**
     * @param int $transactionId
     * @param int $transactionAmount
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction\Collection $transactions
     * @param int|null $orderId
     * @return $this
     */
    protected function setUnused($transactionId, $transactionAmount, $transactions, $orderId = null)
    {
        /** @var \Swarming\StoreCredit\Api\Data\TransactionInterface $transactionLink */
        foreach ($transactions as $transactionLink) {
            $undo = min($transactionLink->getUsed(), $transactionAmount);
            $transactionLink->addUsed(-$undo);
            $this->transactionRepository->save($transactionLink);

            $this->addLink($transactionId, $transactionLink->getTransactionId(), $undo, $orderId);

            $transactionAmount -= $undo;
            if ($transactionAmount == 0) {
                break;
            }
        }
        return $this;
    }

    /**
     * @param int $transactionId
     * @param int $transactionAmount
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction\Collection $transactions
     * @param int|null $orderId
     * @return $this
     */
    protected function setUsed($transactionId, $transactionAmount, $transactions, $orderId = null)
    {
        /** @var \Swarming\StoreCredit\Api\Data\TransactionInterface $transactionLink */
        foreach ($transactions as $transactionLink) {
            $used = min($transactionLink->getUnused(), $transactionAmount);
            $transactionLink->addUsed($used);
            $this->transactionRepository->save($transactionLink);

            $this->addLink($transactionId, $transactionLink->getTransactionId(), $used, $orderId);

            $transactionAmount -= $used;
            if ($transactionAmount == 0) {
                break;
            }
        }
        return $this;
    }
}
