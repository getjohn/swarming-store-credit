<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class Transaction
{
    /**
     * @var string[]
     */
    private $gainTypes = [];

    /**
     * @param array $gainTypes
     */
    public function __construct(array $gainTypes)
    {
        $this->gainTypes = $gainTypes;
    }

    /**
     * @return string[]
     */
    public function getGainTypes()
    {
        return $this->gainTypes;
    }
}
