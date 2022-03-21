<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Quote;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as ResourceModelAbstractDb;
use Swarming\StoreCredit\Api\Data\QuoteAttributeInterface;

class Attribute extends ResourceModelAbstractDb
{
    const TABLE_NAME = 'swarming_credit_quote_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, QuoteAttributeInterface::ATTRIBUTE_ID);
    }
}
