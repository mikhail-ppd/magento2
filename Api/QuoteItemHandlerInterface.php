<?php

namespace Elisa\ProductApi\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

interface QuoteItemHandlerInterface
{
    /**
     * Get BuyRequest Data Object for requested product
     *
     * @param ProductInterface $product
     * @param DataObject $requestProduct
     * @return DataObject
     * @throws LocalizedException
     */
    public function getBuyRequest(ProductInterface $product, DataObject $requestProduct): DataObject;

    /**
     * Get list of Product IDs supported by handler
     *
     * @return string[]
     */
    public function getProductTypeIds(): array;

    /**
     * Update custom price for requested product
     *
     * @param Item $item
     * @param ProductInterface $product
     * @param DataObject $requestProduct
     * @return Item
     */
    public function updateCustomPrice(Item $item, ProductInterface $product, DataObject $requestProduct): Item;
}
