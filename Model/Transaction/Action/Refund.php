<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction\Action;

use Swarming\StoreCredit\Model\Transaction\ActionAbstract;
use Swarming\StoreCredit\Model\Transaction\ActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Refund extends ActionAbstract implements ActionInterface
{
    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateCredits($credits, $transaction)
    {
        if ($transaction->getAmount() > $credits->getTotalSpent()) {
            throw new LocalizedException(__('Not enough credits was spent'));
        }
        $this->checkCreditsLimit($credits, $transaction);
        $credits->addTotalSpent(-$transaction->getAmount());
        $credits->addBalance($transaction->getAmount());
        return $this;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     */
    public function saveTransactionLinks($transaction)
    {
        $refundTransactions = $this->transactionCollectionFactory->create();
        $refundTransactions->filterForRefund($transaction->getCustomerId(), $transaction->getOrderId());
        $this->setUnused($transaction->getTransactionId(), $transaction->getAmount(), $refundTransactions, $transaction->getOrderId());

        $spendTransactions = $this->transactionCollectionFactory->create();
        $spendTransactions->filterSpent($transaction->getCustomerId(), $transaction->getOrderId());
        $this->setUsed($transaction->getTransactionId(), $transaction->getAmount(), $spendTransactions, $transaction->getOrderId());

        return $this;
    }


}
