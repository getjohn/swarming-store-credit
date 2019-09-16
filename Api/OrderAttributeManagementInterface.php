<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
