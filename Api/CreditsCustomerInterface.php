<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface CreditsCustomerInterface
{
    /**
     * @param int $customerId
     * @param mixed[] $adjustmentData
     * @return int
     * @throws \Exception
     * @throws \Magento\Framework\Validator\Exception
     */
    public function update($customerId, array $adjustmentData);

    /**
     * @param int[] $customersId
     * @param mixed[] $adjustmentData
     * @return void
     * @throws \Exception
     * @throws \Magento\Framework\Validator\Exception
     */
    public function massUpdate(array $customersId, array $adjustmentData);
}
