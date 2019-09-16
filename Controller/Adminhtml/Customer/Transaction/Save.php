<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Controller\Adminhtml\Customer\Transaction;

use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditsCustomerInterface
     */
    private $creditsCustomer;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
    ) {
        $this->creditsCustomer = $creditsCustomer;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');
        $adjustmentData = (array)$this->getRequest()->getParam('adjustment');

        try {
            $this->creditsCustomer->update($customerId, $adjustmentData);
            $this->messageManager->addSuccessMessage(__('Credits are saved.'));
        } catch (ValidatorException $e) {
            foreach ($e->getMessages() as $message) {
                $this->messageManager->addMessage($message);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('An error occurred while data saving.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }
}
