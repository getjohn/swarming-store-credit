<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
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
