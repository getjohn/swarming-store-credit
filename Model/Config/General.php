<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class General
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $configShareCustomer;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Config\Share $configShareCustomer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Config\Share $configShareCustomer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configShareCustomer = $configShareCustomer;
    }

    /**
     * @param string $configPath
     * @param int $storeId
     * @return string
     */
    protected function getProtectedValue($configPath, $storeId)
    {
        return $this->configShareCustomer->isWebsiteScope()
            ? $this->getStoreValue($configPath, $storeId)
            : $this->scopeConfig->getValue($configPath, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param string $configPath
     * @param int|string|null $storeId
     * @return string|int
     */
    protected function getStoreValue($configPath, $storeId)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param string $configPath
     * @param int|string|null $storeId
     * @return bool
     */
    protected function isSetStoreFlag($configPath, $storeId)
    {
        return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return $this->isSetStoreFlag('swarming_credits/general/active', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getExchangeRate($storeId = null)
    {
        $rate = (string)$this->getStoreValue('swarming_credits/general/exchange_rate', $storeId);
        $rate = explode('/', $rate);
        if (count($rate) == 2) {
            $rateData = ['base' => $rate[0], 'credits' => $rate[1]];
        } else {
            $rateData = ['base' => 1, 'credits' => 1];
        }
        return $rateData;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isAllowedFractional($storeId = null)
    {
        return (bool)$this->getProtectedValue('swarming_credits/general/allow_fractional', $storeId);
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getMaxAmount($storeId = null)
    {
        return (int)$this->getProtectedValue('swarming_credits/general/max', $storeId);
    }
}
