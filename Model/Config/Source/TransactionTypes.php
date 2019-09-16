<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Config\Source;

class TransactionTypes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string[]
     */
    private $types;

    /**
     * @param array $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->types;
    }

    /**
     * @param string $type
     * @return string
     *
     * @throws \DomainException
     */
    public function getLabel($type)
    {
        if (empty($this->types[$type])) {
            throw new \DomainException('Transaction is not supported.');
        }
        return $this->types[$type];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = [];
        foreach ($this->types as $value => $label) {
            $option[] = ['value' => $value, 'label' => $label];
        }
        return $option;
    }
}
