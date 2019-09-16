<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Controller\Adminhtml\Customer\Transaction;

use Magento\Framework\Validator\Exception as ValidatorException;
use Magento\Framework\Controller\ResultFactory;
use Swarming\StoreCredit\Api\Data\CreditInterface;
use Magento\Framework\Exception\LocalizedException;

class MassSave extends \Magento\Backend\App\Action
{
    /**
     * @var \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory
     */
    private $creditsCollectionFactory;

    /**
     * @var \Swarming\StoreCredit\Api\CreditsCustomerInterface
     */
    private $creditsCustomer;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory $creditsCollectionFactory
     * @param \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Swarming\StoreCredit\Model\ResourceModel\Credit\CollectionFactory $creditsCollectionFactory,
        \Swarming\StoreCredit\Api\CreditsCustomerInterface $creditsCustomer
    ) {
        $this->creditsCollectionFactory = $creditsCollectionFactory;
        $this->creditsCustomer = $creditsCustomer;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $customerIds = $this->getSelectedCustomerIds();
        $adjustmentData = (array)$this->getRequest()->getParam('adjustment');
        $adjustmentData = $this->processRequestData($adjustmentData);

        try {
            $this->creditsCustomer->massUpdate($customerIds, $adjustmentData);
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

    /**
     * @return int[]
     */
    private function getSelectedCustomerIds()
    {
        $excludeMode = $this->getRequest()->getParam('excludeMode');
        $excluded = $this->getRequest()->getParam('excluded');
        $selected = $this->getRequest()->getParam('selected');

        $creditsCollection = $this->creditsCollectionFactory->create();
        if ($excludeMode === 'true' && is_array($excluded) && !empty($excluded)) {
            $creditsCollection->addFieldToFilter(CreditInterface::CUSTOMER_ID, ['nin' => $excluded]);
        } elseif ($excludeMode === 'false' && is_array($selected) && !empty($selected)) {
            $creditsCollection->addFieldToFilter(CreditInterface::CUSTOMER_ID, ['in' => $selected]);
        }

        return $creditsCollection->getColumnValues(CreditInterface::CUSTOMER_ID);
    }

    /**
     * @param array $adjustmentData
     * @return array
     */
    protected function processRequestData(array $adjustmentData)
    {
        return $adjustmentData;
    }
}
