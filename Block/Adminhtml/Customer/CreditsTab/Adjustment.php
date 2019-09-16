<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Block\Adminhtml\Customer\CreditsTab;

use Magento\Customer\Controller\RegistryConstants;
use Swarming\StoreCredit\Api\Data\TransactionInterface;

class Adjustment extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Swarming\StoreCredit\Block\Adminhtml\Transaction\Form\Field\Options\Type
     */
    private $transactionTypeOptions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Swarming\StoreCredit\Block\Adminhtml\Transaction\Form\Field\Options\Type $transactionTypeOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Swarming\StoreCredit\Block\Adminhtml\Transaction\Form\Field\Options\Type $transactionTypeOptions,
        array $data = []
    ) {
        $this->transactionTypeOptions = $transactionTypeOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setUseContainer(true);
        $form->setMethod('post');
        $form->setId('swarming_credits_adjustment');
        $form->setHtmlIdPrefix('swarming_credits_');
        $form->setFieldNameSuffix('adjustment');

        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $form->setAction($this->getUrl('swarming_credits/customer_transaction/save', ['id' => $customerId]));

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Adjustment')]);

        $fieldset->addField(
            TransactionInterface::AMOUNT,
            'text',
            [
                'label' => __('Amount'),
                'title' => __('Amount'),
                'name'  => TransactionInterface::AMOUNT,
                'required' => true,
                'class' => 'validate-number validate-greater-than-zero'
            ]
        );

        $fieldset->addField(
            TransactionInterface::TYPE,
            'select',
            [
                'label' => __('Action'),
                'title' => __('Action'),
                'name'  => TransactionInterface::TYPE,
                'options' => $this->transactionTypeOptions->getOptions(),
                'value' => '',
                'required' => true
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

        $fieldset->addField(
            'submit',
            'submit',
            [
                'name'  => 'submit',
                'value' => __('Submit'),
                'title' => __('Submit'),
                'class' => 'abs-action-default'
            ]
        );

        $this->setForm($form);
        return $this;
    }
}
