<?php

namespace Elisa\ProductApi\Model\ProductDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Elisa\ProductApi\Api\ProductDataBuilderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Grouped implements ProductDataBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function execute(
        ProductDataInterface $productData,
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): ?array {
        $matchParent = $parentProduct && $parentProduct->getTypeId() === GroupedType::TYPE_CODE;

        if (($product instanceof Product) === false
            || ($matchParent && ($parentProduct instanceof Product) === false)) {
            return null;
        }

        $matchProduct = $product->getTypeId() === GroupedType::TYPE_CODE;

        if (!$matchParent && $matchProduct) {
            return $product->getTypeInstance()->getAssociatedProducts($product);
        }

        return null;
    }
}
