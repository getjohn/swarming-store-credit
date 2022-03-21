<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
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
