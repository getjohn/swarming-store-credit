<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Link;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Swarming\StoreCredit\Model\Link;
use Swarming\StoreCredit\Model\ResourceModel\Link as ResourceModelLink;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Link::class, ResourceModelLink::class);
    }
}
