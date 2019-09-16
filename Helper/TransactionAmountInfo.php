<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class TransactionAmountInfo
{
    /**
     * @var \Swarming\StoreCredit\Model\Transaction\InfoRegistry
     */
    private $transactionInfoRegistry;

    /**
     * @param \Swarming\StoreCredit\Model\Transaction\InfoRegistry $transactionInfoRegistry
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Transaction\InfoRegistry $transactionInfoRegistry
    ) {
        $this->transactionInfoRegistry = $transactionInfoRegistry;
    }

    /**
     * @param int $transactionType
     * @param float $used
     * @param float $amount
     * @param string $atTime
     * @param int $storeId
     * @return \Magento\Framework\Phrase|string
     */
    public function getMessage($transactionType, $used, $amount, $atTime, $storeId)
    {
        return $this->transactionInfoRegistry->has($transactionType)
            ? $this->transactionInfoRegistry->get($transactionType)->getMessage($used, $amount, $atTime, $storeId)
            : '';
    }
}
