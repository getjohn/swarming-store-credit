<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\Transaction;

interface ActionInterface
{
    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     */
    public function updateCredits($credits, $transaction);

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return $this
     */
    public function saveTransactionLinks($transaction);
}
