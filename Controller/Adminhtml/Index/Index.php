<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Credits list action
     *
     * @return \Magento\Backend\Model\View\Result\Page||\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Swarming_StoreCredit::credits_list');

        $resultPage->addBreadcrumb(__('Swarming'), __('Swarming'));
        $resultPage->addBreadcrumb(__('Store Credit'), __('Store Credit'));

        return $resultPage;
    }
}
