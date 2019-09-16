<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Creditmemo;

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
     * @var \Swarming\StoreCredit\Helper\OrderStatusHistory
     */
    private $orderStatusHistoryHelper;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
     */
    private $invoiceAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface
     */
    private $invoiceAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
     * @param \Swarming\StoreCredit\Helper\OrderStatusHistory $orderStatusHistoryHelper
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer,
        \Swarming\StoreCredit\Helper\OrderStatusHistory $orderStatusHistoryHelper,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository,
        \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement,
        \Swarming\StoreCredit\Api\InvoiceAttributeRepositoryInterface $invoiceAttributeRepository
    ) {
        $this->creditsCurrency = $creditsCurrency;
        $this->creditsCustomer = $creditsCustomer;
        $this->orderStatusHistoryHelper = $orderStatusHistoryHelper;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->invoiceAttributeManagement = $invoiceAttributeManagement;
        $this->invoiceAttributeRepository = $invoiceAttributeRepository;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoCredits
     * @return void
     */
    public function process($creditmemo, $creditmemoCredits)
    {
        $order = $creditmemo->getOrder();
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);

        $this->updateOrderAttribute($orderCredits, $creditmemoCredits);
        if ($creditmemo->getInvoice()) {
            $this->updateInvoiceAttribute($creditmemo->getInvoice(), $creditmemoCredits);
        }

        $this->processCreditsTransaction(
            $order->getCustomerId(),
            $creditmemoCredits,
            $creditmemo->getIncrementId(),
            $orderCredits->getOrderId(),
            $order->getIncrementId(),
            ($creditmemo->getInvoice() ? $creditmemo->getInvoice()->getEntityId() : null),
            ($creditmemo->getInvoice() ? $creditmemo->getInvoice()->getIncrementId() : null)
        );
        $this->addOrderHistoryComment($order, $creditmemoCredits);
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoCredits
     * @return void
     */
    private function updateOrderAttribute($orderCredits, $creditmemoCredits)
    {
        $creditsRefunded = $orderCredits->getCreditsRefunded() + $creditmemoCredits->getCreditsRefunded();
        $creditsAmountRefunded = $orderCredits->getAmountRefunded() + $creditmemoCredits->getAmountRefunded();
        $creditsBaseAmountRefunded = $orderCredits->getBaseAmountRefunded() + $creditmemoCredits->getBaseAmountRefunded();

        $orderCredits->setCreditsRefunded($creditsRefunded);
        $orderCredits->setAmountRefunded($creditsAmountRefunded);
        $orderCredits->setBaseAmountRefunded($creditsBaseAmountRefunded);

        $this->orderAttributeRepository->save($orderCredits);
    }

    /**
     * @param \Magento\Sales\Api\Data\InvoiceInterface $invoice
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoCredits
     * @return void
     */
    private function updateInvoiceAttribute($invoice, $creditmemoCredits)
    {
        $invoiceCredits = $this->invoiceAttributeManagement->getForInvoice($invoice);

        $creditsRefunded = $invoiceCredits->getCreditsRefunded() + $creditmemoCredits->getCreditsRefunded();
        $creditsAmountRefunded = $invoiceCredits->getAmountRefunded() + $creditmemoCredits->getAmountRefunded();
        $creditsBaseAmountRefunded = $invoiceCredits->getBaseAmountRefunded() + $creditmemoCredits->getBaseAmountRefunded();

        $invoiceCredits->setCreditsRefunded($creditsRefunded);
        $invoiceCredits->setAmountRefunded($creditsAmountRefunded);
        $invoiceCredits->setBaseAmountRefunded($creditsBaseAmountRefunded);

        $this->invoiceAttributeRepository->save($invoiceCredits);
    }

    /**
     * @param int $customerId
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoCredits
     * @param string $creditmemoIncrementId
     * @param int $orderId
     * @param string $orderIncrementId
     * @param int|null $invoiceId
     * @param string|null $invoiceIncrementId
     * @return void
     */
    private function processCreditsTransaction(
        $customerId,
        $creditmemoCredits,
        $creditmemoIncrementId,
        $orderId,
        $orderIncrementId,
        $invoiceId,
        $invoiceIncrementId
    ) {
        $creditsRefunded = min($creditmemoCredits->getCreditsRefunded(), $creditmemoCredits->getCredits());
        if ($creditsRefunded > 0.01) {
            $this->addCreditsTransaction(
                $customerId,
                Transaction::TYPE_REFUND,
                $creditsRefunded,
                $creditmemoCredits->getCreditmemoId(),
                $creditmemoIncrementId,
                $orderId,
                $orderIncrementId,
                $invoiceId,
                $invoiceIncrementId
            );
        }

        $creditsAdded = $creditmemoCredits->getCreditsRefunded() - $creditmemoCredits->getCredits();
        if ($creditsAdded > 0.01) {
            $this->addCreditsTransaction(
                $customerId,
                Transaction::TYPE_ADD,
                $creditsAdded,
                $creditmemoCredits->getCreditmemoId(),
                $creditmemoIncrementId,
                $orderId,
                $orderIncrementId,
                $invoiceId,
                $invoiceIncrementId
            );
        }
    }

    /**
     * @param int $customerId
     * @param string $type
     * @param float $amount
     * @param int $creditmemoId
     * @param string $creditmemoIncrementId
     * @param int $orderId
     * @param string $orderIncrementId
     * @param int|null $invoiceId
     * @param string|null $invoiceIncrementId
     * @return void
     */
    private function addCreditsTransaction(
        $customerId,
        $type,
        $amount,
        $creditmemoId,
        $creditmemoIncrementId,
        $orderId,
        $orderIncrementId,
        $invoiceId,
        $invoiceIncrementId
    ) {
        $transactionData = [
            'type' => $type,
            'order_id' => $orderId,
            'creditmemo_id' => $creditmemoId,
            'amount' => $amount,
            'summary' => __(
                'Order {order}%1{order}, creditmemo {creditmemo}%2{creditmemo}',
                $orderIncrementId,
                $creditmemoIncrementId
            )
        ];

        if ($invoiceId) {
            $transactionData['invoice_id'] = $invoiceId;
            $transactionData['summary'] = __(
                'Order {order}%1{order}, invoice {invoice}%2{invoice}, creditmemo {creditmemo}%3{creditmemo}',
                $orderIncrementId,
                $invoiceIncrementId,
                $creditmemoIncrementId
            );
        }

        $this->creditsCustomer->update($customerId, $transactionData);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface $creditmemoCredits
     * @return void
     */
    private function addOrderHistoryComment($order, $creditmemoCredits)
    {
        $creditsRefundedFormatted = $this->creditsCurrency->format(
            $creditmemoCredits->getCreditsRefunded(),
            ConfigDisplay::FORMAT_HTML_FREE,
            $order->getStoreId()
        );

        $this->orderStatusHistoryHelper->addComment($order, __('Refunded amount of %1', $creditsRefundedFormatted));
    }
}
