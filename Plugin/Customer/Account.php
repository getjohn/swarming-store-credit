<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Plugin\Customer;

use Magento\Customer\Api\AccountManagementInterface;

class Account
{
    /**
     * @var \Swarming\StoreCredit\Api\CreditsRepositoryInterface
     */
    private $creditRepository;

    /**
     * @param \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
     */
    public function __construct(
        \Swarming\StoreCredit\Api\CreditsRepositoryInterface $creditRepository
    ) {
        $this->creditRepository = $creditRepository;
    }

    /**
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateAccountWithPasswordHash(AccountManagementInterface $subject, $customer)
    {
        if ($customer->getId()) {
            $credits = $this->creditRepository->getNew();
            $credits->setCustomerId($customer->getId());
            $this->creditRepository->save($credits);
        }
        return $customer;
    }
}
