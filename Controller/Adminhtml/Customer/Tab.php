<?php
/**
 * Copyright © Swarming Technology, LLC. Covered by the 3-clause BSD license.
 */
namespace Swarming\StoreCredit\Controller\Adminhtml\Customer;

use Magento\Framework\Controller\ResultFactory;

class Tab extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        return $resultLayout;
    }
}
