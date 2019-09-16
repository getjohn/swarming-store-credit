<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Model\Transaction;

use Magento\Framework\App\Area;
use Magento\Framework\DataObject;

class Notifier
{
    /**
     * @var \Swarming\StoreCredit\Model\Config\Notification
     */
    private $configNotification;

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
     * @var \Swarming\StoreCredit\Helper\Store
     */
    private $storeHelper;

    /**
     * @var \Swarming\StoreCredit\Model\Transaction\Notifier\Template
     */
    private $notifierTemplate;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @param \Swarming\StoreCredit\Model\Config\Notification $configNotification
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Swarming\StoreCredit\Helper\Store $storeHelper
     * @param \Swarming\StoreCredit\Model\Transaction\Notifier\Template $notifierTemplate
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\Notification $configNotification,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Swarming\StoreCredit\Helper\Store $storeHelper,
        \Swarming\StoreCredit\Model\Transaction\Notifier\Template $notifierTemplate,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->configNotification = $configNotification;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->storeHelper = $storeHelper;
        $this->notifierTemplate = $notifierTemplate;
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return void
     * @throws \Exception
     */
    public function notify($transaction)
    {
        $this->inlineTranslation->suspend();

        try {
            $customer = $this->customerRegistry->retrieve($transaction->getCustomerId());
            $store = $this->storeHelper->getStore($transaction->getCustomerId(), $transaction->getOrderId());

            $this->transportBuilder->setTemplateIdentifier($this->configNotification->getTransactionTemplate($transaction->getType(), $store->getId()));
            $this->transportBuilder->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $store->getId()]);
            $this->transportBuilder->setTemplateVars($this->getTemplateVars($transaction, $customer, $store));
            $this->transportBuilder->setFrom($this->configNotification->getEmailSender($store->getId()));
            $this->transportBuilder->addTo($customer->getEmail(), $customer->getName());

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            throw $e;
        }

        $this->inlineTranslation->resume();
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return array
     */
    public function getTemplateVars($transaction, $customer, $store)
    {
        $templateVars = $this->notifierTemplate->getTemplateVars($transaction, $customer, $store);

        $transport = new DataObject([
            'transaction' => $transaction,
            'customer' => $customer,
            'store' => $store,
            'vars' => $templateVars
        ]);
        $this->eventManager->dispatch('swarming_credits_transaction_email_vars', ['transport' => $transport]);

        return (array)$transport->getData('vars');
    }
}
