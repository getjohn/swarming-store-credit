<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Order\Invoice\Total;

class Credits extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    const CODE = 'swarming_credits_paid';

    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    private $priceCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface
     */
    private $invoiceAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Api\InvoiceCreditsInterface
     */
    private $invoiceCredits;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     * @param \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement
     * @param \Swarming\StoreCredit\Api\InvoiceCreditsInterface $invoiceCredits
     * @param array $data
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository,
        \Swarming\StoreCredit\Api\InvoiceAttributeManagementInterface $invoiceAttributeManagement,
        \Swarming\StoreCredit\Api\InvoiceCreditsInterface $invoiceCredits,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->creditsCurrency = $creditsCurrency;
        $this->priceCurrency = $priceCurrency;
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->invoiceAttributeManagement = $invoiceAttributeManagement;
        $this->invoiceCredits = $invoiceCredits;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();
        $storeId = $order->getStoreId();

        if (!$this->configGeneral->isActive($storeId) || !$invoice->getOrder()->getCustomerId()) {
            return $this;
        }

        $credits = $this->invoiceCredits->getMaxAllowed($invoice);

        $creditsBaseAmount = $this->creditsCurrency->convertCreditsToBase($credits, $storeId);
        $creditsAmount = $this->priceCurrency->convert($creditsBaseAmount, $storeId);

        $invoiceCredits = $this->invoiceAttributeManagement->getForInvoice($invoice);
        $invoiceCredits->setCredits($credits);
        $invoiceCredits->setAmount(-$creditsAmount);
        $invoiceCredits->setBaseAmount(-$creditsBaseAmount);

        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoiceCredits->getAmount());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoiceCredits->getBaseAmount());
        return $this;
    }
}
