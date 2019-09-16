<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Swarming\StoreCredit\Api\Data\TransactionInterface;

class TransactionCustomer implements \Swarming\StoreCredit\Api\TransactionCustomerInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsAccountantInterface
     */
    private $creditsAccountant;

    /**
     * @var \Swarming\StoreCredit\Api\TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var \Swarming\StoreCredit\Model\Transaction\ActionFactory
     */
    private $transactionActionFactory;

    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Transaction
     */
    private $transactionResourceModel;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Whether to do recalculation for each action
     *
     * @var bool
     */
    private $recalculate;

    /**
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Swarming\StoreCredit\Api\CreditsAccountantInterface $creditsAccountant
     * @param \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Swarming\StoreCredit\Model\Transaction\ActionFactory $transactionActionFactory
     * @param \Swarming\StoreCredit\Model\ResourceModel\Transaction $transactionResourceModel
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param bool $recalculate
     */
    public function __construct(
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Swarming\StoreCredit\Api\CreditsAccountantInterface $creditsAccountant,
        \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository,
        \Swarming\StoreCredit\Model\Transaction\ActionFactory $transactionActionFactory,
        \Swarming\StoreCredit\Model\ResourceModel\Transaction $transactionResourceModel,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $recalculate = true
    ) {
        $this->creditRepository = $creditRepository;
        $this->creditsAccountant = $creditsAccountant;
        $this->transactionRepository = $transactionRepository;
        $this->transactionActionFactory = $transactionActionFactory;
        $this->transactionResourceModel = $transactionResourceModel;
        $this->eventManager = $eventManager;
        $this->recalculate = $recalculate;
    }

    /**
     * @param int $customerId
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return void
     * @throws \Exception
     */
    public function addTransaction($customerId, TransactionInterface $transaction)
    {
        try {
            $this->transactionResourceModel->beginTransaction();

            $transactionType = $this->transactionActionFactory->create($transaction->getType());

            $credits = $this->loadCustomerCredits($customerId);
            $transactionType->updateCredits($credits, $transaction);
            $this->creditRepository->save($credits);

            $this->saveTransaction($transaction, $credits);
            $transactionType->saveTransactionLinks($transaction);

            $this->eventManager->dispatch('swarming_credits_transaction_add_after', ['transaction' => $transaction]);

            $this->transactionResourceModel->commit();
        } catch (\Exception $e) {
            $this->transactionResourceModel->rollBack();
            throw $e;
        }
    }

    /**
     * @param int $customerId
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws LocalizedException
     */
    private function loadCustomerCredits($customerId)
    {
        try {
            $credits = $this->creditRepository->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Credits for the customer are not found.'), $e);
        }

        if ($this->recalculate) {
            $this->creditsAccountant->recalculateAll($credits);
        }

        return $credits;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @return void
     */
    private function saveTransaction($transaction, $credits)
    {
        $transaction->setCustomerId($credits->getCustomerId());
        $transaction->setBalance($credits->getBalance());
        $this->transactionRepository->save($transaction);
    }
}
