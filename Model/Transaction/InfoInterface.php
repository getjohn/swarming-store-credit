<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction;

interface InfoInterface
{
    /**
     * @param float $used
     * @param float $amount
     * @param string $atTime
     * @param int $storeId
     * @return string
     */
    public function getMessage($used, $amount, $atTime, $storeId);
}
