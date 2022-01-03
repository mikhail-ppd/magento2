<?php

namespace Elisa\ProductApi\Plugin\Api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterface;

class OrderRepository
{

    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;


    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    public function afterGetItems(OrderSearchResultInterface $subject, $searchResult)
    {
        foreach ($searchResult as &$order) {
            $refId = $order->getData('ref_id');
            if ($refId) {
                $extensionAttributes = $order->getExtensionAttributes();
                $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
                $extensionAttributes->setRefId($refId);
                $order->setExtensionAttributes($extensionAttributes);
            }
        }

        return $searchResult;
    }
}
