<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction\Action;

use Swarming\StoreCredit\Model\Transaction\ActionAbstract;
use Swarming\StoreCredit\Model\Transaction\ActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Subtract extends ActionAbstract implements ActionInterface
{
    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     * @throws LocalizedException
     */
    public function updateCredits($credits, $transaction)
    {
        if ($transaction->getAmount() > $credits->getBalance()) {
            throw new LocalizedException(__('Not enough credits'));
        }
        $credits->addBalance(-$transaction->getAmount());
        return $this;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     */
    public function saveTransactionLinks($transaction)
    {
        $transactions = $this->transactionCollectionFactory->create();
        $storeId = $this->storeHelper->getStoreId($transaction->getCustomerId());
        $transactions->filterAvailable($transaction->getCustomerId(), $this->configExpiration->getLifeTime($storeId));
        $this->setUsed($transaction->getTransactionId(), $transaction->getAmount(), $transactions);

        return $this;
    }
}
