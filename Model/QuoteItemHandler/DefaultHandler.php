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
    /** @var string[] */
    protected $productTypeIds;

    /**
     * @param string[] $productTypeIds
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(array $productTypeIds, DataObjectFactory $dataObjectFactory)
    {
        $this->productTypeIds = array_filter(array_values($productTypeIds));
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @inheritDoc
     */
    public function getBuyRequest(ProductInterface $product, DataObject $requestProduct): DataObject
    {
        return $this->dataObjectFactory->create([
            'data' => [
                'product' => $product->getId(),
                'qty' => $requestProduct->getData('qty') ?? 1
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
