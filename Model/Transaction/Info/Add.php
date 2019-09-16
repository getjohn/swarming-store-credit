<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction\Info;

use Swarming\StoreCredit\Model\Transaction\InfoInterface;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Add implements InfoInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Expiration
     */
    private $configExpiration;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Helper\Expiration
     */
    private $creditsExpiration;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Helper\Expiration $creditsExpiration
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Helper\Expiration $creditsExpiration
    ) {
        $this->configExpiration = $configExpiration;
        $this->creditsCurrency = $creditsCurrency;
        $this->creditsExpiration = $creditsExpiration;
    }

    /**
     * @param float $used
     * @param float $amount
     * @param string $atTime
     * @param int $storeId
     * @return string
     */
    public function getMessage($used, $amount, $atTime, $storeId)
    {
        $unused = $amount - $used;
        $unusedAmount = $this->creditsCurrency->format($unused, ConfigDisplay::FORMAT_GRID, $storeId);

        if ($this->configExpiration->getLifeTime($storeId) && $unused >= 0.01) {
            return $this->getExpirationMessage($unusedAmount, $atTime, $storeId);
        }
        return '';
    }

    /**
     * @param float $unusedAmount
     * @param string $atTime
     * @param int $storeId
     * @return string
     */
    private function getExpirationMessage($unusedAmount, $atTime, $storeId)
    {
        return $this->creditsExpiration->getExpirationDate($atTime, $storeId, false) > new \DateTime('now')
            ? (string)__('%1 will expire on %2.', $unusedAmount, $this->creditsExpiration->getExpirationDate($atTime))
            : (string)__('%1 was expired.', $unusedAmount);
    }
}
