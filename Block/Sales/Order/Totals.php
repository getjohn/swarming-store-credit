<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Sales\Order;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Swarming\StoreCredit\Model\Quote\Address\Total\Credits as QuoteTotalCredits;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    protected $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    protected $creditsCurrency;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $dataObjectFactory;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    protected $orderAttributeManagement;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     * @param \Magento\Framework\DataObject\Factory $dataObjectFactory
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Directory\Model\PriceCurrency $priceCurrency,
        \Magento\Framework\DataObject\Factory $dataObjectFactory,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        array $data = []
    ) {
        $this->configDisplay = $configDisplay;
        $this->creditsCurrency = $creditsCurrency;
        $this->priceCurrency = $priceCurrency;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->orderAttributeManagement = $orderAttributeManagement;
        parent::__construct($context, $data);
    }

    /**
     * @return bool|\Magento\Sales\Block\Order\Totals
     */
    protected function getOrderTotals()
    {
        $parentBlock = $this->getParentBlock();
        if ($parentBlock instanceof \Magento\Sales\Block\Order\Totals) {
            return $parentBlock;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $orderTotals = $this->getOrderTotals();
        if (!$orderTotals) {
            return $this;
        }

        $order = $orderTotals->getOrder();
        $this->addOrderTotal($order, $orderTotals);

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Sales\Block\Order\Totals $orderTotals
     * @return void
     */
    protected function addOrderTotal($order, $orderTotals)
    {
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCredits() == 0) {
            return;
        }

        $orderTotals->addTotal(
            $this->dataObjectFactory->create([
                'code' => QuoteTotalCredits::CODE,
                'is_formated' => true,
                'value' => $this->creditsCurrency->format($orderCredits->getCredits(), ConfigDisplay::FORMAT_TOTAL, $order->getStoreId(), $orderCredits->getAmount()),
                'base_value' => $this->priceCurrency->format($orderCredits->getBaseAmount(), false),
                'label' => $this->configDisplay->getName($order->getStoreId())
            ])
        );
    }
}
