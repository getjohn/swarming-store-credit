<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Customer;

use Magento\Customer\Block\Account\SortLinkInterface;

class AccountNavigation extends \Magento\Framework\View\Element\Html\Link\Current implements SortLinkInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        array $data = []
    ) {
        $this->configDisplay = $configDisplay;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return 'swarming_credits';
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
