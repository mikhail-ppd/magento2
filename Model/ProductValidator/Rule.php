<?php

namespace Elisa\ProductApi\Model\ProductValidator;

use Elisa\ProductApi\Api\ProductValidatorInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class Rule implements ProductValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function execute(ProductInterface $product, ?ProductInterface $parentProduct = null): bool
    {
        if ($parentProduct || ($product instanceof \Magento\Catalog\Model\Product) === false) {
            return true;
        }

        //todo rule-based validation
        return true;
    }
}
