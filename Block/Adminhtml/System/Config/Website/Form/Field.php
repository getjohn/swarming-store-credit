<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\System\Config\Website\Form;

class Field extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    private $configShareCustomer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\Config\Share $configShareCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Config\Share $configShareCustomer,
        array $data = []
    ) {
        $this->configShareCustomer = $configShareCustomer;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!empty($this->getRequest()->getParam('website')) && !$this->configShareCustomer->isWebsiteScope()) {
            $element->setComment('');
            return '<p>' . __('This option is available on Website scope only if configuration setting: Customers > Customer Configuration > Account Sharing Options > Share Customer Accounts = Per Website') . '</p>';
        }
        return parent::_getElementHtml($element);
    }


    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderInheritCheckbox(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!empty($this->getRequest()->getParam('website')) && !$this->configShareCustomer->isWebsiteScope()) {
            return '<td/>';
        }
        return parent::_renderInheritCheckbox($element);
    }
}
