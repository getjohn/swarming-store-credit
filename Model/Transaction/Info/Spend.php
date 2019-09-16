<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction\Info;

use Swarming\StoreCredit\Model\Transaction\InfoInterface;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Spend implements InfoInterface
{
    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     */
    public function __construct(
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency
    ) {
        $this->creditsCurrency = $creditsCurrency;
    }

    /**
     * @param float $used
     * @param float $amount
     * @param string $atTime
     * @param int $storeId
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getMessage($used, $amount, $atTime, $storeId)
    {
        $usedAmount = $this->creditsCurrency->format($used, ConfigDisplay::FORMAT_GRID, $storeId);

        return $used > 0.01 ? (string)__('%1 Refunded.', $usedAmount) : '';
    }
}
