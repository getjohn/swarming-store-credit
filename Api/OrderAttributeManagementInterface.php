<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface OrderAttributeManagementInterface
{
    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function getForOrder($order);
}
