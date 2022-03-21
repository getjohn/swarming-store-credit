<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface CreditsManagementInterface
{
    /**
     * @param int $cartId
     * @param float $amount
     * @return void
     */
    public function applyCredits($cartId, $amount);
}
