<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Transaction\Form\Field\Options;

class Type
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Source\TransactionTypes
     */
    private $transactionTypes;

    /**
     * @var string[]
     */
    private $adminTransactions;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes
     * @param array $adminTransactions
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes,
        array $adminTransactions
    ) {
        $this->transactionTypes = $transactionTypes;
        $this->adminTransactions = $adminTransactions;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return ['' => __('Please select type')]
            + array_intersect_key($this->transactionTypes->toArray(), $this->adminTransactions);
    }
}
