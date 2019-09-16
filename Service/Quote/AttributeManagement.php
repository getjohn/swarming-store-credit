<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Service\Quote;

use Magento\Quote\Api\Data\CartInterface;

class AttributeManagement implements \Swarming\StoreCredit\Api\QuoteAttributeManagementInterface
{
    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface
     */
    private $quoteAttributeRepository;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionAttributesFactory;

    /**
     * @param \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface $quoteAttributeRepository
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface $quoteAttributeRepository,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionAttributesFactory
    ) {
        $this->quoteAttributeRepository = $quoteAttributeRepository;
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    /**
     * @param \Magento\Quote\Api\Data\CartInterface $cart
     * @return \Swarming\StoreCredit\Api\Data\QuoteAttributeInterface
     */
    public function getForCart($cart)
    {
        $cartAttributes = $cart->getExtensionAttributes() ?: $this->extensionAttributesFactory->create(CartInterface::class);
        $cart->setExtensionAttributes($cartAttributes);

        $cartCredits = $cartAttributes->getCredits();
        if (!$cartCredits) {
            $cartCredits = $cart->getId()
                ? $this->quoteAttributeRepository->getByQuoteId($cart->getId(), true)
                : $this->quoteAttributeRepository->getNew();

            $cartAttributes->setCredits($cartCredits);
        }

        $cartCredits->setQuoteId($cart->getId());
        return $cartCredits;
    }
}
