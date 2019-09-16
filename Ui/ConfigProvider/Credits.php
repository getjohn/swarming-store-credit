<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Ui\ConfigProvider;

use Swarming\StoreCredit\Helper\Currency as CreditsCurrency;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class Credits implements \Magento\Checkout\Model\ConfigProviderInterface
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
     * @var \Swarming\StoreCredit\Model\Config\Spending
     */
    private $configSpending;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Directory\Model\PriceCurrency
     */
    private $priceCurrency;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Model\Config\Spending $configSpending
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Directory\Model\PriceCurrency $priceCurrency
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Model\Config\Spending $configSpending,
        \Magento\Customer\Model\Session $customerSession,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Directory\Model\PriceCurrency $priceCurrency
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        $this->configSpending = $configSpending;
        $this->customerSession = $customerSession;
        $this->creditRepository = $creditRepository;
        $this->creditsCurrency = $creditsCurrency;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        if ($this->configGeneral->isActive()) {
            $config = [
                'swarming' => [
                    'creditsCustomerData' => $this->getCreditsCustomerData(),
                    'creditsConfigData' => $this->getCreditsConfigData()
                ]
            ];
        }

        return $config;
    }

    /**
     * @return array
     */
    private function getCreditsConfigData()
    {
        return [
            'block_title' => $this->configDisplay->getBlockTitle(),
            'name' => $this->configDisplay->getName(),
            'symbol' => $this->configDisplay->getSymbol(),
            'icon' => $this->configDisplay->getIconHtml(),
            'precision' => $this->configGeneral->isAllowedFractional() ? CreditsCurrency::PRECISION : 0,
            'base_format' => $this->configDisplay->getFormat(ConfigDisplay::FORMAT_BASE),
            'total_format' => $this->configDisplay->getFormat(ConfigDisplay::FORMAT_TOTAL),
            'error_message' => $this->configSpending->getErrorMessage()
        ];
    }

    /**
     * @return array
     */
    private function getCreditsCustomerData()
    {
        $creditsData = [];
        if ($this->customerSession->isLoggedIn()) {
            $creditsData = $this->fetchCustomerCredits()->toArray();
            $creditsData['base_amount'] = $creditsData['balance'] ? $this->creditsCurrency->convertCreditsToBase($creditsData['balance']) : 0;
            $creditsData['amount'] = $creditsData['base_amount'] ? $this->priceCurrency->convertAndRound($creditsData['base_amount']) : 0;
        }
        return $creditsData;
    }

    /**
     * @return \Swarming\StoreCredit\Api\Data\CreditInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function fetchCustomerCredits()
    {
        return $this->creditRepository->getByCustomerId($this->customerSession->getCustomerId());
    }
}
