<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Order\Creditmemo;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as ResourceModelAbstractDb;
use Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface;

class Attribute extends ResourceModelAbstractDb
{
    const TABLE_NAME = 'swarming_credit_order_creditmemo_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CreditmemoAttributeInterface::ATTRIBUTE_ID);
    }
}
