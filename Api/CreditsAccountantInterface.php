<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
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
