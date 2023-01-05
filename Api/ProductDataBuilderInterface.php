<?php

namespace Elisa\ProductApi\Api;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;

interface ProductDataBuilderInterface
{
    /**
     * Builds Elisa Product Data from Catalog Product Entity
     * Returns children catalog products for further processing, if any
     *
     * @param ProductDataInterface $productData
     * @param ProductInterface $product
     * @param ProductInterface|null $parentProduct
     * @return ProductInterface|null Additional child products
     * @throws LocalizedException
     */
    public function execute(
        ProductDataInterface $productData,
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): ?array;
}
