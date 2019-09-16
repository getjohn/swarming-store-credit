<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

use Swarming\StoreCredit\Api\Data\TransactionInterface;

/**
 * @api
 */
interface TransactionCustomerInterface
{
    /**
     * @param int $customerId
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return void
     * @throws \Exception
     */
    public function addTransaction($customerId, TransactionInterface $transaction);
}
