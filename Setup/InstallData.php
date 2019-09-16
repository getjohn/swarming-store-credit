<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Swarming\StoreCredit\Model\Credits\Repairer
     */
    private $creditsRepairer;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $pageFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $blockFactory;

    /**
     * @param \Swarming\StoreCredit\Model\Credits\Repairer $creditsRepairer
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Credits\Repairer $creditsRepairer,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory
    ) {
        $this->creditsRepairer = $creditsRepairer;
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->creditsRepairer->repair();

        $this->addCmsBlock();
    }

    /**
     * @return void
     */
    private function addCmsBlock()
    {
        /* Create credits info static block */
        $creditsInfoBlock = $this->blockFactory->create();
        $creditsInfoBlock->setData([
            'title' => 'Swarming Credits Info',
            'identifier' => 'swarming_credits_info',
            'content' => "<p>Our website {{config path='general/store_information/name'}} gives you the opportunity to"
                . " spend store credit in addition to real currency.</p>\r\n"
                . "<p>You can use Store Credit as discount for future purchases at our store.</p>",
            'is_active' => 1,
            'stores' => [0],
        ]);
        $creditsInfoBlock->save();
    }
}
