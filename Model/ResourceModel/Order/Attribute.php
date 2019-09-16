<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as ResourceModelAbstractDb;
use Swarming\StoreCredit\Api\Data\OrderAttributeInterface;

class Attribute extends ResourceModelAbstractDb
{
    const TABLE_NAME = 'swarming_credit_order_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, OrderAttributeInterface::ATTRIBUTE_ID);
    }
}
