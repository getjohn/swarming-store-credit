<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Service\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;

class AttributeManagement implements \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface
     */
    private $orderAttributeRepository;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Api\OrderAttributeRepositoryInterface $orderAttributeRepository,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->orderAttributeRepository = $orderAttributeRepository;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Swarming\StoreCredit\Api\Data\OrderAttributeInterface
     */
    public function getForOrder($order)
    {
        // Fetching the extension attributes for the order
        $orderAttributes = $order->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(OrderInterface::class);
        $order->setExtensionAttributes($orderAttributes);

        // Getting the credits associated with the order
        $orderCredits = $orderAttributes->getCredits();
        if (!$orderCredits) {
            $orderCredits = $order->getEntityId()
                ? $this->orderAttributeRepository->getByOrderId($order->getEntityId(), true)
                : $this->orderAttributeRepository->getNew();

            $orderAttributes->setCredits($orderCredits);
        }

        $orderCredits->setOrderId($order->getEntityId());

        // Validate and process the store credit amount
        $this->validateStoreCreditAmount($orderCredits);

        return $orderCredits;
    }

    /**
     * Validates the store credit amount, ensuring it's numeric and properly formatted.
     * 
     * @param \Swarming\StoreCredit\Api\Data\OrderAttributeInterface $orderCredits
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validateStoreCreditAmount($orderCredits)
    {
        // Get the store credit amount (assuming it's stored in 'credit_amount' or a similar field)
        $amount = $orderCredits->getCreditAmount();

        // Step 1: Replace commas with periods (handle European-style numbers)
        $amount = str_replace(',', '.', $amount);

        // Step 2: Check if the amount is numeric
        if (!is_numeric($amount)) {
            throw new LocalizedException(__('Amount must be a number.'));
        }

        // Optionally, you can also format the amount or take further action, 
        // such as saving the formatted value back to the object.
        $orderCredits->setCreditAmount((float)$amount);
    }
}
