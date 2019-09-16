<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api;

/**
 * @api
 */
interface CreditsAccountantInterface
{
    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return \Swarming\StoreCredit\Api\CreditsAccountantInterface
     */
    public function recalculateBalance($credits);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return \Swarming\StoreCredit\Api\CreditsAccountantInterface
     */
    public function recalculateTotalSpent($credits);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return \Swarming\StoreCredit\Api\CreditsAccountantInterface
     */
    public function recalculateTotalHeld($credits);

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return \Swarming\StoreCredit\Api\CreditsAccountantInterface
     */
    public function recalculateAll($credits);
}
