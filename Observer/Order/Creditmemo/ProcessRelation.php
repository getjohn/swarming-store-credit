<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Api\Data\CreditmemoInterface;

class ProcessRelation implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Refund
     */
    private $configRefund;

    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
     */
    private $creditmemoAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Model\Order\Creditmemo\ProcessorRelation
     */
    private $creditmemoRelationProcessor;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement
     * @param \Swarming\StoreCredit\Model\Order\Creditmemo\ProcessorRelation $creditmemoRelationProcessor
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface $creditmemoAttributeManagement,
        \Swarming\StoreCredit\Model\Order\Creditmemo\ProcessorRelation $creditmemoRelationProcessor
    ) {
        $this->configRefund = $configRefund;
        $this->creditmemoAttributeManagement = $creditmemoAttributeManagement;
        $this->creditmemoRelationProcessor = $creditmemoRelationProcessor;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return bool
     */
    private function isRefunded($creditmemo)
    {
        return $creditmemo->getState() == Creditmemo::STATE_REFUNDED
            && $creditmemo->getOrigData(CreditmemoInterface::STATE) != Creditmemo::STATE_REFUNDED;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getData('object');

        if (!$this->configRefund->isActive($creditmemo->getStoreId())
            || !$this->configRefund->isRefundEnabled($creditmemo->getStoreId())
            || !$creditmemo->getOrder()->getCustomerId()
        ) {
            return;
        }

        if (!$this->isRefunded($creditmemo)) {
            return;
        }

        $creditmemoCredits = $this->creditmemoAttributeManagement->getForCreditmemo($creditmemo);
        if ($creditmemoCredits->getCreditsRefunded() < 0.01) {
            return;
        }

        $this->creditmemoRelationProcessor->process($creditmemo, $creditmemoCredits);
    }
}
