<?php

namespace Elisa\ProductApi\Model\QuoteItemHandler;

use Elisa\ProductApi\Api\QuoteItemHandlerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Model\Quote\Item;

class DefaultHandler implements QuoteItemHandlerInterface
{
    /** @var DataObjectFactory */
    protected $dataObjectFactory;
    /** @var InventoryManagement */
    protected $inventoryManagement;
    /** @var string[] */
    protected $productTypeIds;

    /**
     * @param string[] $productTypeIds
     * @param DataObjectFactory $dataObjectFactory
     * @param InventoryManagement $inventoryManagement
     */
    public function __construct(
        array $productTypeIds,
        DataObjectFactory $dataObjectFactory,
        InventoryManagement $inventoryManagement
    ) {
        $this->productTypeIds = array_filter(array_values($productTypeIds));
        $this->dataObjectFactory = $dataObjectFactory;
        $this->inventoryManagement = $inventoryManagement;
    }

    /**
     * @inheritDoc
     */
    public function getBuyRequest(ProductInterface $product, DataObject $requestProduct): DataObject
    {
        if (!$this->inventoryManagement->isSalable($product)) {
            return $this->dataObjectFactory->create([
                'data' => [
                    'product' => (int)$product->getId(),
                    'qty' => 0
                ]
            ]);
        }

        $qtyToAdd = $requestProduct->getData('qty') ?? 1;
        $maxSaleQty = $this->inventoryManagement->getMaxSaleQty($product);

        if (!$this->inventoryManagement->isBackordersAllowed($product)) {
            $qtyToAdd = min($maxSaleQty, $qtyToAdd);
        }

        $minSaleQty = $this->inventoryManagement->getMinSaleQty($product);
        $qtyToAdd = $maxSaleQty < $minSaleQty ? 0 : max($minSaleQty, $qtyToAdd);

        return $this->dataObjectFactory->create([
            'data' => [
                'product' => (int)$product->getId(),
                'qty' => $qtyToAdd
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getProductTypeIds(): array
    {
        return $this->productTypeIds;
    }

    /**
     * @inheritDoc
     */
    public function updateCustomPrice(Item $item, ProductInterface $product, DataObject $requestProduct): Item
    {
        if (!$requestProduct->hasData('price')) {
            return $item;
        }

        $price = (float)$requestProduct->getData('price');

        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);

        return $item;
    }
}
