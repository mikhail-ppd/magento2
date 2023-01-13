<?php
namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrdersCollectionFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderManagement implements OrderManagementInterface
{
    /** @var OrderExtensionFactory  */
    protected $extensionFactory;
    /**
     * @var OrdersCollectionFactory
     */
    protected $ordersCollectionFactory;

    /**
     * @param OrdersCollectionFactory $ordersCollectionFactory
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(
        OrdersCollectionFactory $ordersCollectionFactory,
        OrderExtensionFactory $extensionFactory
    ) {
        $this->ordersCollectionFactory = $ordersCollectionFactory;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @inheritdoc
     */
    public function addElisaReferenceId(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $refId = $order->getData('ref_id');

        if ($refId) {
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ?: $this->extensionFactory->create();
            $extensionAttributes->setRefId($refId);
            $order->setExtensionAttributes($extensionAttributes);
        }
    }

    /**
     * @inheritdoc
     */
    public function getList(int $timestamp): array
    {
        $ordersCollection = $this->ordersCollectionFactory->create();

        $ordersCollection->getSelect()->joinLeft(
            ['crq' => 'elisa_productsapi_cartrequest_to_quote'],
            "main_table.quote_id = crq.quote_id"
        );

        $ordersCollection->getSelect()->joinLeft(
            ['cr' => 'elisa_productsapi_cartrequest'],
            "crq.ref_id = cr.id"
        );

        $createdAt = date("Y-m-d H:i:s", $timestamp ?: 0);

        $ordersCollection->addFieldToFilter('created_at', ['gt' => $createdAt]);
        $ordersCollection->addFieldToFilter('crq.ref_id', ['notnull' => true]);
        $ordersCollection->getSelect()->group('main_table.entity_id');

        /** @var \Magento\Sales\Api\Data\OrderInterface[] $orders */
        $orders = $ordersCollection->getItems();

        foreach ($orders as $order) {
            $this->addElisaReferenceId($order);
        }

        return $orders;
    }

    /**
     * @inheritDoc
     */
    public function isOrderFromElisa(int $orderId): bool
    {
        $ordersCollection = $this->ordersCollectionFactory->create();

        $ordersCollection->addAttributeToFilter('entity_id', $orderId);

        $ordersCollection->getSelect()->joinInner(
            ['crq' => 'elisa_productsapi_cartrequest_to_quote'],
            "main_table.quote_id = crq.quote_id"
        );

        $ordersCollection->getSelect()->joinInner(
            ['cr' => 'elisa_productsapi_cartrequest'],
            "crq.ref_id = cr.id"
        );

        return $ordersCollection->getSize() > 0;
    }
}
