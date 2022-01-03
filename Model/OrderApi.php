<?php
namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\OrderApiInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrdersCollectionFactory;

class OrderApi implements OrderApiInterface
{
    /**
     * @var OrdersCollectionFactory
     */
    private $ordersCollectionFactory;

    public function __construct(
        OrdersCollectionFactory $ordersCollectionFactory
    ) {
        $this->ordersCollectionFactory = $ordersCollectionFactory;
    }

    /**
     * @param string $timestamp
     * @return OrderSearchResultInterface
     */
    public function getOrders($timestamp)
    {
        $ordersCollection = $this->ordersCollectionFactory->create();
        $ordersCollection->getSelect()->joinLeft(
            ['crq' => 'elisa_productsapi_cartrequest_to_quote'],
            "main_table.quote_id = crq.quote_id",
        );
        $ordersCollection->getSelect()->joinLeft(
            ['cr' => 'elisa_productsapi_cartrequest'],
            "crq.ref_id = cr.id",
        );
        if ($timestamp) {
            $createdAt = date("Y-m-d H:i:s", $timestamp);
        } else {
            $createdAt = date("Y-m-d H:i:s", 0);
        }
        $ordersCollection->addFieldToFilter('created_at', ['gt' => $createdAt]);
        $ordersCollection->addFieldToFilter('crq.ref_id', ['notnull' => true]);
        $ordersCollection->getSelect()->group('main_table.entity_id');

        return $ordersCollection;
    }
}
