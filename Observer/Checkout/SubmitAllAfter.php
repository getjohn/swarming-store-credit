<?php
declare(strict_types=1);

namespace Swarming\StoreCredit\Observer\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Store\Model\ScopeInterface;

class SubmitAllAfter implements ObserverInterface
{
    const PATH_NOTIFICATION_URL = 'getjohn_punchouthub/checkout/swarming_credits_ack_url';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsCustomerInterface
     */
    private $creditsCustomer;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->creditRepository = $creditRepository;
        $this->creditsCustomer = $creditsCustomer;
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();

        $ackURL = $this->_scopeConfig->getValue(self::PATH_NOTIFICATION_URL, ScopeInterface::SCOPE_STORE);
        if(!$ackURL) {
            return;
        }

        if (!$order->getCustomerId()) {
            return;
        }

        $customerId = $order->getCustomerId();
        try {
            $credit = $this->creditRepository->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            return;
        }
        $adjustmentData = [];
        $adjustmentData['amount'] = $credit->getBalance();
        $adjustmentData['type'] = 'subtract';
        $adjustmentData['summary'] = 'Reset the customer credit after the order place.';
        $adjustmentData['submit'] = 'Submit';

        try {
            $this->creditsCustomer->update($customerId, $adjustmentData);
        } catch (ValidatorException $e) {
            foreach ($e->getMessages() as $message) {
                $this->messageManager->addMessage($message);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while data saving.'));
        }
    }
}
