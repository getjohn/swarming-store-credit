<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\Import\Behavior;

class Add extends \Magento\ImportExport\Model\Source\Import\AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND => __('Add Transactions')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'add';
    }
}
