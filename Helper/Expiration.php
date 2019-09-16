<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Expiration
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
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency
    ) {
        $this->configExpiration = $configExpiration;
        $this->creditsCurrency = $creditsCurrency;
    }

    /**
     * @param string $date
     * @param int $storeId
     * @param bool $format
     * @return \DateTime|string
     */
    public function getExpirationDate($date, $storeId = null, $format = true)
    {
        $date = new \DateTime($date);
        $date->add(new \DateInterval('P' . $this->configExpiration->getLifeTime($storeId) . 'D'));
        return $format ? $date->format('M d, Y') : $date;
    }

    /**
     * @param array $expirationData
     * @param int $storeId
     * @return array
     */
    public function prepareExpirationAmounts($expirationData, $storeId)
    {
        foreach ($expirationData as &$rowData) {
            $rowData['date'] = $this->getExpirationDate($rowData['date'], $storeId);
            $rowData['amount'] = $this->creditsCurrency->format($rowData['amount'], ConfigDisplay::FORMAT_BASE, $storeId);
        }
        return $expirationData;
    }
}
