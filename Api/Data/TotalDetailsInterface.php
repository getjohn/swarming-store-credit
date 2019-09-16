<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Api\Data;

interface TotalDetailsInterface
{
    /**
     * @param string|float $credits
     * @return \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
     */
    public function setCredits($credits);

    /**
     * @return float
     */
    public function getCredits();

    /**
     * @param float $maxCredits
     * @return \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
     */
    public function setMaxAllowedCredits($maxCredits);

    /**
     * @return float
     */
    public function getMaxAllowedCredits();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
     */
    public function setCreditsAmount($amount);

    /**
     * @return float
     */
    public function getCreditsAmount();

    /**
     * @param float $amount
     * @return \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
     */
    public function setBaseCreditsAmount($amount);

    /**
     * @return \Swarming\StoreCredit\Api\Data\TotalDetailsInterface
     */
    public function getBaseCreditsAmount();
}
