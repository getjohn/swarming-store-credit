<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Helper;

class TransactionSummary
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $templates = [];

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $templates
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        array $templates = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->templates = $templates;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @return \Magento\Framework\Phrase|string
     */
    public function getSummary($transaction)
    {
        $summary = $transaction->getSummary();

        foreach ($this->templates as $entity => $urlData) {
            $entityId = $this->getEntityId($transaction, $urlData['key']);
            $replacement = $entityId
                ? '<a href="' . $this->urlBuilder->getUrl($urlData['path'], [$urlData['key'] => $entityId, '_nosid' => true]) . '">$1</a>'
                : '$1';
            $summary = preg_replace('/{' . $entity . '}(.+){' . $entity . '}/', $replacement, $summary);
        }

        return $summary;
    }

    /**
     * @param \Swarming\StoreCredit\Api\Data\TransactionInterface $transaction
     * @param string $key
     * @return string
     */
    private function getEntityId($transaction, $key)
    {
        switch ($key) {
            case 'invoice_id':
                $entityId = $transaction->getInvoiceId();
                break;
            case 'creditmemo_id':
                $entityId = $transaction->getCreditmemoId();
                break;
            case 'order_id':
            default:
                $entityId = $transaction->getOrderId();
                break;
        }

        return $entityId;
    }
}
