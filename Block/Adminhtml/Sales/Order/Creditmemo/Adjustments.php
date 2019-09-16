<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Sales\Order\Creditmemo;

use Swarming\StoreCredit\Model\Order\Creditmemo\Total\Credits as CreditmemoTotalCredits;

class Adjustments extends \Magento\Backend\Block\Template
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Refund
     */
    private $configRefund;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Helper\Refund
     */
    private $refundHelper;

    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
     */
    private $creditmemoAttributeManagement;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    private $source;

    /**
     * @var bool
     */
    private $useMaxAvailable = false;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Helper\Refund $refundHelper
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Helper\Refund $refundHelper,
        \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement,
        array $data = []
    ) {
        $this->configRefund = $configRefund;
        $this->configDisplay = $configDisplay;
        $this->creditsCurrency = $creditsCurrency;
        $this->refundHelper = $refundHelper;
        $this->creditmemoAttributeManagement = $creditmemoAttributeManagement;
        parent::__construct($context, $data);
    }

    /**
     * @param bool $useMaxAvailable
     * @return void
     */
    public function useMaxAvailable($useMaxAvailable)
    {
        $this->useMaxAvailable = (bool)$useMaxAvailable;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $this->initSource();
        if (!$this->isAvailable()) {
            return $this;
        }

        $total = new \Magento\Framework\DataObject([
            'code' => CreditmemoTotalCredits::CODE,
            'area' => 'footer',
            'block_name' => $this->getNameInLayout()
        ]);
        $this->getParentBlock()->addTotal($total, 'grand_total');
        return $this;
    }


    /**
     * @return $this
     */
    private function initSource()
    {
        $this->source = $this->getParentBlock()->getSource();
        return $this;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->configRefund->isActive($this->source->getStoreId())
            && $this->configRefund->isRefundEnabled($this->source->getStoreId())
            && $this->source->getOrder()->getCustomerId()
            && $this->getCreditsRefund();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getCreditsRefundLabel()
    {
        return __('%1 Refund', $this->configDisplay->getName($this->source->getStoreId()));
    }

    /**
     * @return float
     */
    public function getCreditsRefund()
    {
        $credits = $this->useMaxAvailable
            ? $this->refundHelper->getMaxCreditsForRefund($this->source)
            : $this->creditmemoAttributeManagement->getForCreditmemo($this->source)->getCreditsRefunded();

        return $this->creditsCurrency->round($credits, $this->source->getStoreId());
    }
}
