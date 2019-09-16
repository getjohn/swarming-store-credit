<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction;

class ActionFactory
{
    /**
     * @var array
     */
    private $types = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $typeMap
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $typeMap
    ) {
        $this->objectManager = $objectManager;
        $this->initTypes($typeMap);
    }

    /**
     * @param array $typeMap
     * @return void
     */
    private function initTypes(array $typeMap)
    {
        foreach ($typeMap as $typeInfo) {
            if (isset($typeInfo['type']) && isset($typeInfo['class'])) {
                $this->types[$typeInfo['type']] = $typeInfo['class'];
            }
        }
    }

    /**
     * @param string $transactionType
     * @return \Swarming\StoreCredit\Model\Transaction\ActionInterface
     */
    public function create($transactionType)
    {
        if (!isset($this->types[$transactionType])) {
            throw new \InvalidArgumentException('Wrong credits transaction type.');
        }

        $transactionAction = $this->objectManager->create($this->types[$transactionType]);

        if (!$transactionAction instanceof ActionInterface) {
            throw new \InvalidArgumentException(get_class($transactionAction) . ' isn\'t instance of ' . ActionInterface::class);
        }

        return $transactionAction;
    }
}
