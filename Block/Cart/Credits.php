<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Cart;

class Credits extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param array $data
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        array $data = []
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        $this->creditRepository = $creditRepository;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->configGeneral->isActive() && $this->getCustomerCreditsBalance() > 0;
    }

    /**
     * @return string
     */
    public function getBlockTitle()
    {
        return $this->configDisplay->getBlockTitle();
    }

    /**
     * @return float
     */
    private function getCustomerCreditsBalance()
    {
        return $this->fetchCustomerCredits()->getBalance();
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface|bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function fetchCustomerCredits()
    {
        return $this->creditRepository->getByCustomerId($this->customerSession->getCustomerId());
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->customerSession->isLoggedIn()) {
            return parent::_toHtml();
        }
        return '';
    }
}
