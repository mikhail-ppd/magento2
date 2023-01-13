<?php

namespace Elisa\ProductApi\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;

interface ProductValidatorInterface
{
    /**
     * Checks if product is valid for Elisa Catalogue
     *
     * @param ProductInterface $product
     * @param ProductInterface|null $parentProduct
     * @return bool whether product is a valid elisa product
     * @throws LocalizedException
     */
    public function execute(
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): bool;
}
