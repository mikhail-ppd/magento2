<?php

namespace Elisa\ProductApi\Model\QuoteItemHandler;

use Elisa\ProductApi\Api\QuoteItemHandlerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

class Configurable implements QuoteItemHandlerInterface
{
    /** @var DataObjectFactory */
    protected $dataObjectFactory;
    /** @var InventoryManagement */
    protected $inventoryManagement;
    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DataObjectFactory $dataObjectFactory
     * @param InventoryManagement $inventoryManagement
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DataObjectFactory $dataObjectFactory,
        InventoryManagement $inventoryManagement
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productRepository = $productRepository;
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

        $childSku = $requestProduct->getData('child_sku') ?? null;

        if (!$childSku) {
            throw new LocalizedException(__("No child item data received."));
        }

        if (is_array($childSku)) {
            $childSku = reset($childSku);
        }

        $childProduct = $this->productRepository->get($childSku, false, $product->getStoreId(), true);

        if (!$this->inventoryManagement->isSalable($childProduct)) {
            return $this->dataObjectFactory->create([
                'data' => [
                    'product' => (int)$product->getId(),
                    'child_product_name' => $childProduct->getName(),
                    'qty' => 0
                ]
            ]);
        }

        /** @var ConfigurableType $productTypeInstance */
        $productTypeInstance = $product->getTypeInstance();
        $productTypeInstance->setStoreFilter($product->getStoreId(), $product);
        $attributes = $productTypeInstance->getConfigurableAttributes($product);

        $superAttributeList = [];

        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getProductAttribute()->getAttributeCode();
            $superAttributeList[$attribute->getAttributeId()] = (int)$childProduct->getData($attributeCode);
        }

        $qtyToAdd = $requestProduct->getData('qty') ?? 1;
        $maxSaleQty = $this->inventoryManagement->getMaxSaleQty($childProduct);

        if (!$this->inventoryManagement->isBackordersAllowed($childProduct)) {
            $qtyToAdd = min($maxSaleQty, $qtyToAdd);
        }

        $minSaleQty = $this->inventoryManagement->getMinSaleQty($childProduct);
        $qtyToAdd = $maxSaleQty < $minSaleQty ? 0 : max($minSaleQty, $qtyToAdd);

        return $this->dataObjectFactory->create([
            'data' => [
                'product' => (int)$product->getId(),
                'child_product_name' => $childProduct->getName(),
                'qty' => $qtyToAdd,
                'super_attribute' => $superAttributeList
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getProductTypeIds(): array
    {
        return [ConfigurableType::TYPE_CODE];
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
