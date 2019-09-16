<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer;

class Info extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Swarming\StoreCredit\Helper\TransactionAmountInfo
     */
    private $transactionAmountInfo;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Swarming\StoreCredit\Helper\TransactionAmountInfo $transactionAmountInfo
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Swarming\StoreCredit\Helper\TransactionAmountInfo $transactionAmountInfo,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        array $data = []
    ) {
        $this->transactionAmountInfo = $transactionAmountInfo;
        $this->storeHelper = $storeHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        $storeId = $this->storeHelper->getStoreId($row['customer_id'], $row['order_id']);
        return $this->transactionAmountInfo->getMessage(
            $row->getData('type'),
            (float)parent::_getValue($row),
            $row->getData('amount'),
            $row->getData('at_time'),
            $storeId
        );
    }
}
