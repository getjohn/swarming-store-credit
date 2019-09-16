<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface CreditmemoAttributeManagementInterface
{
    /**
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function getForCreditmemo($creditmemo);
}
