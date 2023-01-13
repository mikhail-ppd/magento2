<?php

namespace Elisa\ProductApi\Api\Data;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterface;

/**
 * @api
 */
interface ElisaProductInterface
{
    /**
     * Get Product Data
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface|null
     */
    public function getProductData(): ?ProductDataInterface;

    /**
     * Get Product Entity ID
     *
     * @return int
     */
    public function getProductId(): int;

    /**
     * Get Product Qty State
     *
     * @return bool|null whether the product has qty or not
     */
    public function isProductTypeWithQty(): ?bool;

    /**
     * Get Stock Data
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterface|null
     */
    public function getStockData(): ?StockDataInterface;

    /**
     * Get Product Validity
     *
     * @return bool whether the product is a valid Elisa Product or could be dropped from Elisa catalogue
     */
    public function isValid(): bool;

    /**
     * Get children products
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProductInterface[]|null
     */
    public function getChildren(): ?array;

    /**
     * Set children products
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProductInterface[] $value
     * @return ElisaProductInterface
     */
    public function setChildren(array $value): ElisaProductInterface;

    /**
     * Set Product Data
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface $value
     * @return ElisaProductInterface
     */
    public function setProductData(ProductDataInterface $value): ElisaProductInterface;

    /**
     * Set Product ID
     *
     * @param int $value
     * @return ElisaProductInterface
     */
    public function setProductId(int $value): ElisaProductInterface;

    /**
     * Set Product Qty State
     *
     * @param bool $value
     * @return ElisaProductInterface
     */
    public function setProductTypeWithQty(bool $value): ElisaProductInterface;

    /**
     * Set Stock Data
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterface $value
     * @return ElisaProductInterface
     */
    public function setStockData(StockDataInterface $value): ElisaProductInterface;

    /**
     * Set Product Validity
     *
     * @param bool $value
     * @return ElisaProductInterface
     */
    public function setValid(bool $value): ElisaProductInterface;
}
