<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Swarming\StoreCredit\Model\Transaction;

class CreditsActions extends Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['add'] = [
                    'url' => $this->urlBuilder->getUrl(
                        'swarming_credits/customer_transaction/save',
                        ['id' => $item['customer_id']]
                    ),
                    'title' => __('Add Store Credit'),
                    'label' => __('Add'),
                    'type' => Transaction::TYPE_ADD,
                    'callback' => [
                        'provider' => 'swarmingCreditsAction',
                        'target' => 'action'
                    ]
                ];
                $item[$this->getData('name')]['subtract'] = [
                    'url' => $this->urlBuilder->getUrl(
                        'swarming_credits/customer_transaction/save',
                        ['id' => $item['customer_id']]
                    ),
                    'title' => __('Subtract Store Credit'),
                    'label' => __('Subtract'),
                    'type' => Transaction::TYPE_SUBTRACT,
                    'callback' => [
                        'provider' => 'swarmingCreditsAction',
                        'target' => 'action'
                    ]
                ];
            }
        }
        return $dataSource;
    }
}
