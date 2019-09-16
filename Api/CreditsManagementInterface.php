<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
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
