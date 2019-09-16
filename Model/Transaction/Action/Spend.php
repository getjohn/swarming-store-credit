<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction\Action;

use Swarming\StoreCredit\Model\Transaction\ActionAbstract;
use Swarming\StoreCredit\Model\Transaction\ActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Spend extends ActionAbstract implements ActionInterface
{
    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     * @throws LocalizedException
     */
    public function updateCredits($credits, $transaction)
    {
        if ($transaction->getAmount() > $credits->getTotalHeld()) {
            throw new LocalizedException(__('Not enough credits on hold'));
        }
        $credits->addTotalHeld(-$transaction->getAmount());
        $credits->addTotalSpent($transaction->getAmount());
        return $this;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     */
    public function saveTransactionLinks($transaction)
    {
        $transactions = $this->transactionCollectionFactory->create();
        $transactions->filterHeld($transaction->getCustomerId(), $transaction->getOrderId());
        $this->setUsed($transaction->getTransactionId(), $transaction->getAmount(), $transactions, $transaction->getOrderId());

        return $this;
    }
}
