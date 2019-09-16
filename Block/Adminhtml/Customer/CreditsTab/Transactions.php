<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Customer\CreditsTab;

use Magento\Customer\Controller\RegistryConstants;

class Transactions extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('swarming_credit_transactions_grid');
        $this->setDefaultSort('transaction_id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory
            ->getReport('swarming_credits_transactions_listing_data_source')
            ->addFieldToFilter('customer_id', $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'transaction_id',
            ['header' => __('ID'), 'width' => '100', 'index' => 'transaction_id', 'type' => 'number']
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type',
                'filter' => 'Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Filter\Type',
                'renderer' => 'Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer\Type'
            ]
        );

        $this->addColumn(
            'amount',
            [
                'header' => __('Amount'),
                'index' => 'amount',
                'type' => 'number',
                'renderer' => 'Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer\Credits'
            ]
        );

        $this->addColumn(
            'used',
            [
                'header' => __('Info'),
                'index' => 'used',
                'filter' => false,
                'renderer' => 'Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer\Info'
            ]
        );

        $this->addColumn(
            'summary',
            [
                'header' => __('Summary'),
                'index' => 'summary',
                'renderer' => 'Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer\Summary'
            ]
        );

        $this->addColumn(
            'balance',
            [
                'header' => __('Balance'),
                'index' => 'balance',
                'type' => 'number',
                'renderer' => 'Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer\Credits'
            ]
        );

        $this->addColumn(
            'at_time',
            ['header' => __('Created'), 'index' => 'at_time', 'type' => 'datetime']
        );

        return parent::_prepareColumns();
    }

    /**
     * @param \Swarming\StoreCredit\Model\Transaction|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '#';
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('swarming_credits/customer/transactions', ['_current' => true]);
    }
}
