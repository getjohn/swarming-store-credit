<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Order\Creditmemo;

use Magento\Sales\Api\Data\CreditmemoInterface;

class AttributeManagement implements \Swarming\StoreCredit\Api\CreditmemoAttributeManagementInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface
     */
    private $creditmemoAttributeRepository;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface $creditmemoAttributeRepository
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Api\CreditmemoAttributeRepositoryInterface $creditmemoAttributeRepository,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->creditmemoAttributeRepository = $creditmemoAttributeRepository;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     * @return \Swarming\StoreCredit\Api\Data\CreditmemoAttributeInterface
     */
    public function getForCreditmemo($creditmemo)
    {
        $creditmemoAttributes = $creditmemo->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(CreditmemoInterface::class);
        $creditmemo->setExtensionAttributes($creditmemoAttributes);

        $creditmemoCredits = $creditmemoAttributes->getCredits();
        if (!$creditmemoCredits) {
            $creditmemoCredits = $creditmemo->getEntityId()
                ? $this->creditmemoAttributeRepository->getByCreditmemoId($creditmemo->getEntityId(), true)
                : $this->creditmemoAttributeRepository->getNew();

            $creditmemoAttributes->setCredits($creditmemoCredits);
        }

        $creditmemoCredits->setCreditmemoId($creditmemo->getEntityId());
        return $creditmemoCredits;
    }
}
