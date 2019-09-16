<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Filter;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var array
     */
    private $types;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Source\TransactionTypes
     */
    private $transactionTypes;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes,
        array $data = []
    ) {
        $this->transactionTypes = $transactionTypes;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->types = [0 => ''] + $this->transactionTypes->toArray();
        parent::_construct();
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $options = [];
        foreach ($this->types as $type => $label) {
            $options[] = ['value' => $type, 'label' => $label];
        }

        return $options;
    }

    /**
     * @return array|null
     */
    public function getCondition()
    {
        return $this->getValue() === null ? null : ['eq' => $this->getValue()];
    }
}
