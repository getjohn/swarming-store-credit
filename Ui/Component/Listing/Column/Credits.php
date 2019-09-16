<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Credits extends Column
{
    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->creditsCurrency = $creditsCurrency;
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
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
                $storeId = $this->getStoreIdByCustomerId($item['customer_id']);
                $item[$this->getData('name')] = $this->creditsCurrency->format($item[$this->getData('name')], ConfigDisplay::FORMAT_GRID, $storeId);
            }
        }
        return $dataSource;
    }

    /**
     * @param int $customerId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStoreIdByCustomerId($customerId)
    {
        $customer = $this->customerRegistry->retrieve($customerId);
        return $this->storeManager->getWebsite($customer->getWebsiteId())->getDefaultStore()->getId();
    }
}
