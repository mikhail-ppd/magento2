<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct;

/**
 * @api
 */
interface StockDataInterface
{
    /**
     * Get Quantity
     *
     * @return float|null Quantity in inventory
     */
    public function getQty(): ?float;

    /**
     * Get Salable Quantity
     *
     * @return float|null Quantity that is available for sale
     */
    public function getSalableQty(): ?float;

    /**
     * Get whether the item is salable
     *
     * @return bool Whether the item is salable or not
     */
    public function isSalable(): bool;

    /**
     * Get whether the item has unlimited inventory
     *
     * @return bool|null Whether the item has unlimited inventory or not
     */
    public function isWithUnlimitedInventory(): ?bool;

    /**
     * Set Quantity
     *
     * @param float $value
     * @return StockDataInterface
     */
    public function setQty(float $value): StockDataInterface;

    /**
     * Set whether the item is salable
     *
     * @param bool $value
     * @return StockDataInterface
     */
    public function setSalable(bool $value): StockDataInterface;

    /**
     * Set Salable Quantity
     *
     * @param float $value
     * @return StockDataInterface
     */
    public function setSalableQty(float $value): StockDataInterface;

    /**
     * Set whether the item is salable
     *
     * @param bool $value
     * @return StockDataInterface
     */
    public function setWithUnlimitedInventory(bool $value): StockDataInterface;
}
