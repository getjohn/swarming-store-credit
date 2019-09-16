<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Ui\Component;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        /** @var \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult $collection */
        $collection = $this->getSearchResult();
        $collection->join(
            ['customer' => 'customer_grid_flat'],
            'main_table.customer_id = customer.entity_id',
            ['name', 'email', 'group_id', 'website_id']
        );
        return $this->searchResultToOutput($collection);
    }
}
