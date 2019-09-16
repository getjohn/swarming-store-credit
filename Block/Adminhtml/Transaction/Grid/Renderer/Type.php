<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer;

class Type extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Source\TransactionTypes
     */
    private $transactionTypes;

    /**
     * @var array
     */
    private $types;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Swarming\StoreCredit\Model\Config\Source\TransactionTypes $transactionTypes,
        array $data = []
    ) {
        $this->transactionTypes = $transactionTypes;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->types = $this->transactionTypes->toArray();
        parent::_construct();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return $this->getType($row->getType());
    }

    /**
     * @param string $type
     * @return \Magento\Framework\Phrase
     */
    public function getType($type)
    {
        return isset($this->types[$type]) ? $this->types[$type] : __('Unknown');
    }
}
