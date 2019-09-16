<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Customer\Controller\AbstractAccount
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
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Model\Config\Display $configDisplay
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Model\Config\Display $configDisplay
    ) {
        $this->configGeneral = $configGeneral;
        $this->configDisplay = $configDisplay;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        if (!$this->configGeneral->isActive()) {
            /** @var \Magento\Framework\Controller\Result\Forward $forwardResult */
            $forwardResult = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $forwardResult->forward('noroute');
            return $forwardResult;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('My %creditsName', ['creditsName' => $this->configDisplay->getName()]));
        return $resultPage;
    }
}
