<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\ResourceModel\Order\Invoice;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb as ResourceModelAbstractDb;
use Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface;

class Attribute extends ResourceModelAbstractDb
{
    const TABLE_NAME = 'swarming_credit_order_invoice_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, InvoiceAttributeInterface::ATTRIBUTE_ID);
    }
}
