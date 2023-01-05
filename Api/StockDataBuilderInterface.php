<?php

namespace Elisa\ProductApi\Api;

use Elisa\ProductApi\Api\Data\ElisaProductInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;

interface StockDataBuilderInterface
{
    /**
     * Adds stock data to Elisa Product from Catalog Product entity
     *
     * @param ElisaProductInterface $elisaProduct
     * @param ProductInterface $product
     * @throws LocalizedException
     */
    public function execute(ElisaProductInterface $elisaProduct, ProductInterface $product);
}
