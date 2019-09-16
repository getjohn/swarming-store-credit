<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Swarming\StoreCredit\Model\Config\Backend\Image\Icon;
use Magento\Store\Model\ScopeInterface;

class Display
{
    /**
     * Format types
     */
    const FORMAT_BASE = 'base';

    const FORMAT_HTML_FREE = 'html_free';

    const FORMAT_GRID = 'grid';

    const FORMAT_TOTAL = 'total';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getBlockTitle($storeId = null)
    {
        return (string)$this->scopeConfig->getValue('swarming_credits/display/block_title', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getName($storeId = null)
    {
        return (string)$this->scopeConfig->getValue('swarming_credits/display/name', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getIcon($storeId = null)
    {
        return (string)$this->scopeConfig->getValue('swarming_credits/display/icon', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getIconUrl($storeId = null)
    {
        return $this->urlBuilder->getBaseUrl(['_type' => 'media']) . Icon::UPLOAD_DIR . '/' . $this->getIcon($storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getIconHtml($storeId = null)
    {
        return $this->getIcon() ? '<img class="swarming-credits-icon" src="' . $this->getIconUrl($storeId) . '" />' : '';
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getSymbol($storeId = null)
    {
        return (string)$this->scopeConfig->getValue('swarming_credits/display/symbol', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param string $formatType
     * @param int|null $storeId
     * @return string
     */
    public function getFormat($formatType, $storeId = null)
    {
        return (string)$this->scopeConfig->getValue("swarming_credits/display/{$formatType}_format", ScopeInterface::SCOPE_STORE, $storeId);
    }
}
