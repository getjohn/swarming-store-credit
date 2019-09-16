<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block;

use Magento\Customer\Block\Account\SortLinkInterface;

class TopLink extends \Magento\Framework\View\Element\Html\Link implements SortLinkInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->configGeneral->isActive()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('swarming_credits');
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return (string)__('My %creditsName', ['creditsName' => $this->configDisplay->getName()]);
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }
}
