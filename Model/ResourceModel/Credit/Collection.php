<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Credit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Swarming\StoreCredit\Model\Credit;
use Swarming\StoreCredit\Model\ResourceModel\Credit as ResourceModelCredit;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Credit::class, ResourceModelCredit::class);
    }
}
