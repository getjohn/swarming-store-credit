<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Sales\Order;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;

class CreditmemoRepository
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface
     */
    private $creditmemoAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface $creditmemoAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface $creditmemoAttributeRepository
    ) {
        $this->creditmemoAttributeRepository = $creditmemoAttributeRepository;
    }

    /**
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return \Magento\Sales\Api\Data\CreditmemoInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(CreditmemoRepositoryInterface $subject, \Closure $proceed, CreditmemoInterface $creditmemo)
    {
        $creditmemoExtension = $creditmemo->getExtensionAttributes();

        $proceed($creditmemo);

        if ($creditmemoExtension && $creditmemoExtension->getCredits()) {
            $creditmemoCredits = $creditmemoExtension->getCredits();
            $creditmemoCredits->setCreditmemoId($creditmemo->getEntityId());
            $this->creditmemoAttributeRepository->save($creditmemoCredits);
        }

        return $creditmemo;
    }
}
