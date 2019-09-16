<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class CartRepository
{
    /**
     * @var \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface
     */
    private $quoteAttributeRepository;

    /**
     * @param \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface $quoteAttributeRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Api\QuoteAttributeRepositoryInterface $quoteAttributeRepository
    ) {
        $this->quoteAttributeRepository = $quoteAttributeRepository;
    }

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Quote\Api\Data\CartInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(CartRepositoryInterface $subject, \Closure $proceed, CartInterface $quote)
    {
        $cartExtension = $quote->getExtensionAttributes();

        $proceed($quote);

        if ($cartExtension && $cartExtension->getCredits()) {
            $quoteCredits = $cartExtension->getCredits();
            $quoteCredits->setQuoteId($quote->getId());
            $this->quoteAttributeRepository->save($quoteCredits);
        }

        return $quote;
    }
}
