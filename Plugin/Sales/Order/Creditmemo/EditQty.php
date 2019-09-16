<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales\Order\Creditmemo;

class EditQty
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
    ) {
        $this->configGeneral = $configGeneral;
        $this->orderAttributeManagement = $orderAttributeManagement;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Items|\Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create\Items $subject
     * @param bool $canEditQty
     * @return bool
     */
    public function afterCanEditQty($subject, $canEditQty)
    {
        $orderCredits = $this->orderAttributeManagement->getForOrder($subject->getSource()->getOrder());
        if ($orderCredits->getCredits() > 0 && !$this->configGeneral->isAllowedFractional($subject->getSource()->getStoreId())) {
            $canEditQty = false;
        }
        return $canEditQty;
    }
}
