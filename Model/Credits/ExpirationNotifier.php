<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Credits;

use Magento\Framework\App\Area;
use Swarming\StoreCredit\Model\Config\Display as ConfigDisplay;

class ExpirationNotifier
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Expiration
     */
    private $configExpiration;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Notification
     */
    private $configNotification;

    /**
     * @var \Swarming\StoreCredit\Model\Config\Display
     */
    private $configDisplay;

    /**
     * @var \Swarming\StoreCredit\Helper\Expiration
     */
    private $creditsExpiration;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    private $creditsCurrency;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Service\TransactionManager
     */
    private $transactionManager;

    /**
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Expiration $configExpiration
     * @param \Swarming\StoreCredit\Model\Config\Notification $configNotification
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     * @param \Swarming\StoreCredit\Helper\Expiration $creditsExpiration
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     * @param \Swarming\StoreCredit\Service\TransactionManager $transactionManager
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Expiration $configExpiration,
        \Swarming\StoreCredit\Model\Config\Notification $configNotification,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay,
        \Swarming\StoreCredit\Helper\Expiration $creditsExpiration,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Swarming\StoreCredit\Service\TransactionManager $transactionManager,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->configExpiration = $configExpiration;
        $this->configNotification = $configNotification;
        $this->configDisplay = $configDisplay;
        $this->creditsExpiration = $creditsExpiration;
        $this->creditsCurrency = $creditsCurrency;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->creditRepository = $creditRepository;
        $this->transactionManager = $transactionManager;
        $this->storeHelper = $storeHelper;
        $this->logger = $logger;
    }

    /**
     * @param int[] $customerIds
     * @param int $defaultStoreId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function notify($customerIds, $defaultStoreId)
    {
        $customersExpirationData = $this->transactionManager->getCustomersExpirationAmounts(
            $customerIds,
            $this->configExpiration->getLifeTime($defaultStoreId),
            $this->configExpiration->getExpirationReminderDays($defaultStoreId),
            $this->configExpiration->getExpirationRepeats($defaultStoreId)
        );

        foreach ($customersExpirationData as $customerId => $expirationData) {
            $customer = $this->customerRegistry->retrieve($customerId);
            $credits = $this->creditRepository->getByCustomerId($customer->getId());
            $this->setEmail($customer, $credits, $expirationData);
            $this->customerRegistry->remove($customerId);
        }
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Swarming\StoreCredit\Api\Data\CreditInterface $credits
     * @param array $expirationData
     * @return void
     */
    private function setEmail($customer, $credits, $expirationData)
    {
        $this->inlineTranslation->suspend();

        $store = $this->storeHelper->getStore($customer->getId());
        $expirationData = $this->creditsExpiration->prepareExpirationAmounts($expirationData, $store->getId());
        try {
            $this->transportBuilder
                ->setTemplateIdentifier($this->configExpiration->getExpirationTemplate($store->getId()))
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()])
                ->setTemplateVars([
                    'life_time' => $this->configExpiration->getLifeTime($store->getId()),
                    'credits_name' => $this->configDisplay->getName($store->getId()),
                    'credits_balance' => $this->creditsCurrency->format($credits->getBalance(), ConfigDisplay::FORMAT_BASE, $store->getId()),
                    'customer' => $customer,
                    'store' => $store,
                    'website_name' => $store->getWebsite()->getName(),
                    'expiration_items' => $expirationData
                ])
                ->setFrom($this->configNotification->getEmailSender($store->getId()))
                ->addTo($customer->getEmail(), $customer->getName());

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        $this->inlineTranslation->resume();
    }
}
