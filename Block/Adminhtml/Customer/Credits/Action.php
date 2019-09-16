<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Customer\Credits;

use Swarming\StoreCredit\Api\Data\TransactionInterface;

class Action extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setUseContainer(true);
        $form->setId('swarming_credits_action');
        $form->setHtmlIdPrefix('swarming_credits_action_');

        $fieldset = $form->addFieldset('base_fieldset', []);

        $fieldset->addField(
            TransactionInterface::AMOUNT,
            'text',
            [
                'label' => __('Amount'),
                'title' => __('Amount'),
                'name'  => TransactionInterface::AMOUNT,
                'required' => true,
                'class' => 'validate-digits validate-greater-than-zero'
            ]
        );

        $fieldset->addField(
            TransactionInterface::SUMMARY,
            'textarea',
            [
                'label' => __('Summary'),
                'title' => __('Summary'),
                'name'  => TransactionInterface::SUMMARY
            ]
        );

        $fieldset->addField(
            TransactionInterface::SUPPRESS_NOTIFICATION,
            'checkbox',
            [
                'label' => __('Suppress Email'),
                'title' => __('Suppress Email'),
                'name'  => TransactionInterface::SUPPRESS_NOTIFICATION,
                'value' => 1
            ]
        );

        $this->setForm($form);
        return $this;
    }
}
