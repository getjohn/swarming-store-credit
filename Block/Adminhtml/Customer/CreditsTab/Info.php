<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Customer\CreditsTab;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Controller\RegistryConstants;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Info extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    private $credits;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->customerRegistry = $customerRegistry;
        $this->creditRepository = $creditRepository;
        $this->creditsCurrency = $creditsCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    private function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->fetchCustomer();
        }
        return $this->customer;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function fetchCustomer()
    {
        $customerId = $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $this->customerRegistry->retrieve($customerId);
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface|bool
     */
    public function getCredits()
    {
        if (null === $this->credits) {
            $this->credits = $this->fetchCustomerCredits();
        }
        return $this->credits;
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface|bool
     */
    private function fetchCustomerCredits()
    {
        try {
            $credits = $this->creditRepository->getByCustomerId($this->getCustomer()->getId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return $credits;
    }

    /**
     * @param float $amount
     * @return float
     */
    public function formatCurrency($amount)
    {
        $storeId = $this->_storeManager->getWebsite($this->getCustomer()->getWebsiteId())->getDefaultStore()->getId();
        return $this->creditsCurrency->format($amount, ConfigDisplay::FORMAT_BASE, $storeId);
    }
}
