<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Credits extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        array $data = []
    ) {
        $this->creditsCurrency = $creditsCurrency;
        $this->storeHelper = $storeHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        $amount = (float)parent::_getValue($row);
        $storeId = $this->storeHelper->getStoreId($row['customer_id'], $row['order_id']);
        return $this->creditsCurrency->format($amount, ConfigDisplay::FORMAT_GRID, $storeId);
    }
}
