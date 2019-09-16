<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepairCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Swarming\StoreCredit\Model\Credits\Repairer
     */
    private $creditsRepairer;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Swarming\StoreCredit\Model\Credits\Repairer $creditsRepairer
     * @param string|null $name
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Swarming\StoreCredit\Model\Credits\Repairer $creditsRepairer,
        $name = null
    ) {
        $this->creditsRepairer = $creditsRepairer;
        $this->customerCollectionFactory = $customerCollectionFactory;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('swarming-credits:repair');
        $this->setDescription('Create missing Store Credit records for Customers');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $customerCollection = $this->customerCollectionFactory->create();
        $customerCount = $customerCollection->getSize();

        $progressBar = new \Symfony\Component\Console\Helper\ProgressBar($output, $customerCount);
        $progressBar->setFormat('<comment>%message%</comment>: %current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s%');
        $progressBar->setMessage('Store Credit records repairing');
        $progressBar->display();

        $repairedItems = $this->creditsRepairer->repair(
            function () use ($progressBar) {
                $progressBar->advance();
            }
        );

        $progressBar->finish();
        $output->writeln('');

        $output->writeln(sprintf('<info>Processed</info> %d <info>customers</info>', $customerCount));
        $output->writeln(sprintf('<info>Created</info> %d <info>Store Credit records</info>', $repairedItems));
    }
}
