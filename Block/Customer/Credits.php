<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Customer;

use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;
use Magento\Framework\Exception\NoSuchEntityException;

class Credits extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Swarming\StoreCredit\Api\Data\CreditInterface
     */
    private $credits;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        array $data = []
    ) {
        $this->creditRepository = $creditRepository;
        $this->customerSession = $customerSession;
        $this->creditsCurrency = $creditsCurrency;
        parent::__construct($context, $data);
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
            $credits = $this->creditRepository->getByCustomerId($this->customerSession->getCustomerId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return $credits;
    }

    /**
     * @param float $amount
     * @return float
     */
    public function formatCreditsBase($amount)
    {
        return $this->creditsCurrency->format($amount, ConfigDisplay::FORMAT_BASE);
    }
}
