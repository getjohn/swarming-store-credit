<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction;

class InfoRegistry
{
    /**
     * @var array
     */
    private $types = [];

    /**
     * @var \Swarming\StoreCredit\Model\Transaction\InfoInterface[]
     */
    private $transactionInfo = [];

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
        $this->types = $typeMap;
    }

    /**
     * @param string $transactionType
     * @return bool
     */
    public function has($transactionType)
    {
        return isset($this->types[$transactionType]);
    }

    /**
     * @param string $transactionType
     * @return \Swarming\StoreCredit\Model\Transaction\InfoInterface
     *
     * @throws \InvalidArgumentException
     */
    public function get($transactionType)
    {
        if (empty($this->transactionInfo[$transactionType])) {
            $this->transactionInfo[$transactionType] = $this->create($transactionType);
        }
        return $this->transactionInfo[$transactionType];
    }

    /**
     * @param string $transactionType
     * @return \Swarming\StoreCredit\Model\Transaction\InfoInterface
     *
     * @throws \InvalidArgumentException
     */
    private function create($transactionType)
    {
        if (!$this->has($transactionType)) {
            throw new \InvalidArgumentException('Wrong credits transaction type.');
        }

        $transactionInfo = $this->objectManager->create($this->types[$transactionType]);

        if (!$transactionInfo instanceof InfoInterface) {
            throw new \InvalidArgumentException(get_class($transactionType) . ' isn\'t instance of ' . InfoInterface::class);
        }

        return $transactionInfo;
    }
}
