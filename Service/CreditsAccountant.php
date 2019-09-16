<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

class CreditsAccountant implements \Swarming\StoreCredit\Api\CreditsAccountantInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\TransactionManagerInterface
     */
    private $transactionManager;

    /**
     * @param \Swarming\StoreCredit\Api\TransactionManagerInterface $transactionManager
     */
    public function __construct(
        \Swarming\StoreCredit\Api\TransactionManagerInterface $transactionManager
    ) {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return $this
     */
    public function recalculateBalance($credits)
    {
        $credits->setBalance($this->transactionManager->getBalance($credits->getCustomerId()));
        return $this;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return $this
     */
    public function recalculateTotalSpent($credits)
    {
        $credits->setTotalSpent($this->transactionManager->getTotalSpent($credits->getCustomerId()));
        return $this;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return $this
     */
    public function recalculateTotalHeld($credits)
    {
        $credits->setTotalHeld($this->transactionManager->getTotalHeld($credits->getCustomerId()));
        return $this;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return $this
     */
    public function recalculateAll($credits)
    {
        $this->recalculateBalance($credits);
        $this->recalculateTotalSpent($credits);
        $this->recalculateTotalHeld($credits);
        return $this;
    }
}
