<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Creditmemo\Total;

class Credits extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    const CODE = 'swarming_credits_refunded';

    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
     */
    private $invoiceAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
     */
    private $creditmemoAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
     * @param array $data
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement,
        \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->creditsCurrency = $creditsCurrency;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->invoiceAttributeManagement = $invoiceAttributeManagement;
        $this->creditmemoAttributeManagement = $creditmemoAttributeManagement;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $invoice = $creditmemo->getInvoice();
        $storeId = $order->getStoreId();

        if (!$this->configGeneral->isActive($storeId) || !$creditmemo->getOrder()->getCustomerId()) {
            return $this;
        }

        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        $creditmemoCredits = $this->creditmemoAttributeManagement->getForCreditmemo($creditmemo);

        $creditsAvailable = max(0, ($orderCredits->getCreditsPaid() - $orderCredits->getCreditsRefunded()));
        $creditsAmountAvailable = max(0, abs($orderCredits->getAmountPaid()) - $orderCredits->getAmountRefunded());
        $creditsBaseAmountAvailable = max(0, abs($orderCredits->getBaseAmountPaid()) - $orderCredits->getAmountRefunded());

        $creditsAmountAvailable = min($creditmemo->getGrandTotal(), $creditsAmountAvailable);
        $creditsBaseAmountAvailable = min($creditmemo->getBaseGrandTotal(), $creditsBaseAmountAvailable);
        if ($creditmemo->getGrandTotal() == $creditsAmountAvailable) {
            $creditsAvailable = $this->creditsCurrency->convertBaseToCredits($creditsBaseAmountAvailable, $storeId, true);
        }

        if ($invoice) {
            $creditmemo->setInvoiceId($invoice->getEntityId());
            $invoiceCredits = $this->invoiceAttributeManagement->getForInvoice($invoice);
            $creditsAvailable = min($creditsAvailable, $invoiceCredits->getCredits());
            $creditsAmountAvailable = min($creditsAmountAvailable, abs($invoiceCredits->getAmount()));
            $creditsBaseAmountAvailable = min($creditsBaseAmountAvailable, abs($invoiceCredits->getBaseAmount()));
        }

        $creditsAmountAvailable = max($this->creditsCurrency->convertCreditsToCurrency($creditsAvailable, $storeId), $creditsAmountAvailable);
        $creditsBaseAmountAvailable = max($this->creditsCurrency->convertCreditsToBase($creditsAvailable, $storeId), $creditsBaseAmountAvailable);

        $creditmemoCredits->setCredits($creditsAvailable);
        $creditmemoCredits->setAmount(-$creditsAmountAvailable);
        $creditmemoCredits->setBaseAmount(-$creditsBaseAmountAvailable);

        $creditmemo->setGrandTotal(max(0, $creditmemo->getGrandTotal() - $creditsAmountAvailable));
        $creditmemo->setBaseGrandTotal(max(0, $creditmemo->getBaseGrandTotal() - $creditsBaseAmountAvailable));
        return $this;
    }
}
