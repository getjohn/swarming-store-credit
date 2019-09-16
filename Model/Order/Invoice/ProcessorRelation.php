<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Invoice;

use Swarming\StoreCredit\Model\Transaction;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class ProcessorRelation
{
    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsCustomerInterface
     */
    private $creditsCustomer;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @var \Swarming\StoreCredit\Helper\OrderStatusHistory
     */
    private $orderStatusHistoryHelper;

    /**
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     * @param \Swarming\StoreCredit\Helper\OrderStatusHistory $orderStatusHistoryHelper
     */
    public function __construct(
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository,
        \Swarming\StoreCredit\Helper\OrderStatusHistory $orderStatusHistoryHelper
    ) {
        $this->creditsCurrency = $creditsCurrency;
        $this->creditsCustomer = $creditsCustomer;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->orderStatusHistoryHelper = $orderStatusHistoryHelper;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceCredits
     * @return void
     */
    public function process($invoice, $invoiceCredits)
    {
        $order = $invoice->getOrder();
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);

        $this->updateOrderAttribute($orderCredits, $invoiceCredits);

        $this->addCreditsTransaction(
            $order->getCustomerId(),
            $orderCredits,
            $invoiceCredits,
            $order->getIncrementId(),
            $invoice->getIncrementId()
        );
        $this->addOrderHistoryComment($order, $invoiceCredits);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceCredits
     * @return void
     */
    private function updateOrderAttribute($orderCredits, $invoiceCredits)
    {
        $creditsPaid = $orderCredits->getCreditsPaid() + $invoiceCredits->getCredits();
        $creditsAmountPaid = abs($orderCredits->getAmountPaid()) + abs($invoiceCredits->getAmount());
        $creditsBaseAmountPaid = abs($orderCredits->getBaseAmountPaid()) + abs($invoiceCredits->getBaseAmount());

        $orderCredits->setCreditsPaid($creditsPaid);
        $orderCredits->setAmountPaid(-$creditsAmountPaid);
        $orderCredits->setBaseAmountPaid(-$creditsBaseAmountPaid);

        $this->orderAttributeRepository->save($orderCredits);
    }

    /**
     * @param int $customerId
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceCredits
     * @param string $orderIncrementId
     * @param string $invoiceIncrementId
     * @return void
     */
    private function addCreditsTransaction($customerId, $orderCredits, $invoiceCredits, $orderIncrementId, $invoiceIncrementId)
    {
        $this->creditsCustomer->update(
            $customerId,
            [
                'type' => Transaction::TYPE_SPEND,
                'order_id' => $orderCredits->getOrderId(),
                'invoice_id' => $invoiceCredits->getInvoiceId(),
                'amount' => $invoiceCredits->getCredits(),
                'summary' => __('Order {order}%1{order}, invoice {invoice}%2{invoice}', $orderIncrementId, $invoiceIncrementId)
            ]
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\StoreCredit\Api\Data\InvoiceAttributeInterface $invoiceCredits
     * @return void
     */
    private function addOrderHistoryComment($order, $invoiceCredits)
    {
        $creditsFormatted = $this->creditsCurrency->format(
            $invoiceCredits->getCredits(),
            ConfigDisplay::FORMAT_HTML_FREE,
            $order->getStoreId()
        );

        $this->orderStatusHistoryHelper->addComment($order, __('Spent amount of %1', $creditsFormatted));
    }
}
