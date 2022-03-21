<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Model\Config\Source;

class SpendingLimit implements \Magento\Framework\Option\ArrayInterface
{
    const FIXED = 'fixed';
    const PERCENT = 'percent';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FIXED, 'label' => __('Fixed')],
            ['value' => self::PERCENT, 'label' => __('Percent')]
        ];
    }
}
