<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Observer\Order\Creditmemo;

class AfterSave implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Refund
     */
    private $configRefund;

    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface
     */
    private $creditmemoAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Refund $configRefund
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface $creditmemoAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Refund $configRefund,
        \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface $creditmemoAttributeRepository
    ) {
        $this->configRefund = $configRefund;
        $this->creditmemoAttributeRepository = $creditmemoAttributeRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getData('creditmemo');

        if (!$creditmemo
            || !$this->configRefund->isActive($creditmemo->getStoreId())
            || !$this->configRefund->isRefundEnabled($creditmemo->getStoreId())
        ) {
            return;
        }

        $creditmemoExtension = $creditmemo->getExtensionAttributes();

        if ($creditmemoExtension && $creditmemoExtension->getCredits()) {
            $creditmemoCredits = $creditmemoExtension->getCredits();
            $creditmemoCredits->setCreditmemoId($creditmemo->getEntityId());
            $this->creditmemoAttributeRepository->save($creditmemoCredits);
        }
    }
}
