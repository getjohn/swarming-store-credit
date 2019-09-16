<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Transaction\Grid\Renderer;

class Summary extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Swarming\StoreCredit\Api\TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var \Swarming\StoreCredit\Helper\TransactionSummary
     */
    private $transactionSummery;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository
     * @param \Swarming\StoreCredit\Helper\TransactionSummary $transactionSummery
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Swarming\StoreCredit\Api\TransactionRepositoryInterface $transactionRepository,
        \Swarming\StoreCredit\Helper\TransactionSummary $transactionSummery,
        array $data = []
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionSummery = $transactionSummery;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function _getValue(\Magento\Framework\DataObject $row)
    {
        $transaction = $this->transactionRepository->getNew(['data' => $row->getData()]);
        return $this->transactionSummery->getSummary($transaction);
    }
}
