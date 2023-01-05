<?php

namespace Elisa\ProductApi\Model\QuoteItemHandler;

use Elisa\ProductApi\Api\QuoteItemHandlerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

class Grouped implements QuoteItemHandlerInterface
{
    /** @var DataObjectFactory */
    protected $dataObjectFactory;
    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(ProductRepositoryInterface $productRepository, DataObjectFactory $dataObjectFactory)
    {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function getBuyRequest(ProductInterface $product, DataObject $requestProduct): DataObject
    {
        $childSkuData = $requestProduct->getData('child_skus') ?? [];

        if (!$childSkuData || !is_array($childSkuData)) {
            throw new LocalizedException(__("No child item data received."));
        }

        $groupedOptions = [];

        foreach ($childSkuData as $childSkuDatum) {
            $childSku = $childSkuDatum['sku'] ?? null;

            if (!$childSku) {
                continue;
            }

            $childProduct = $this->productRepository->get($childSku);
            $groupedOptions[$childProduct->getId()] = $childSkuDatum['qty'] ?? 1;
        }

        if (!$groupedOptions) {
            throw new LocalizedException(__("No valid child item data received."));
        }

        return $this->dataObjectFactory->create([
            'data' => [
                'product' => $product->getId(),
                'super_group' => $groupedOptions
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getProductTypeIds(): array
    {
        return [\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE];
    }

    /**
     * @inheritDoc
     */
    public function updateCustomPrice(Item $item, ProductInterface $product, DataObject $requestProduct): Item
    {
        $childSkuData = $requestProduct->getData('child_skus') ?? [];

        $skuPriceMap = [];

        foreach ($childSkuData as $childSkuDatum) {
            $childPrice = (float)$childSkuDatum['price'] ?? 0.0;

            if (!$childPrice) {
                continue;
            }

            $childSku = $childSkuDatum['sku'] ?? null;

            if (!$childSku) {
                continue;
            }

            $skuPriceMap[$childSku] = $childPrice;
        }

        foreach ($item->getChildren() as $childItem) {
            /** @var Item $childItem */
            $childSku = $childItem->getSku();

            if (!isset($skuPriceMap[$childSku])) {
                continue;
            }

            $childItem->setCustomPrice($skuPriceMap[$childSku]);
            $childItem->setOriginalCustomPrice($skuPriceMap[$childSku]);
        }

        return $item;
    }
}
