<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct;

use Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterface;
use Magento\Framework\DataObject;

class StockData extends DataObject implements StockDataInterface
{
    private const KEY_SALABLE = 'salable';
    private const KEY_QTY = 'qty';
    private const KEY_SALABLE_QTY = 'salable_qty';
    private const KEY_WITH_UNLIMITED_INVENTORY = 'with_unlimited_inventory';

    /**
     * @inheritDoc
     */
    public function getQty(): ?float
    {
        return $this->getData(self::KEY_QTY);
    }

    /**
     * @inheritDoc
     */
    public function getSalableQty(): ?float
    {
        return $this->getData(self::KEY_SALABLE_QTY);
    }

    /**
     * @inheritDoc
     */
    public function isSalable(): bool
    {
        return (bool)$this->getData(self::KEY_SALABLE);
    }

    /**
     * @inheritDoc
     */
    public function isWithUnlimitedInventory(): ?bool
    {
        return $this->getData(self::KEY_WITH_UNLIMITED_INVENTORY);
    }

    /**
     * @inheritDoc
     */
    public function setQty(float $value): StockDataInterface
    {
        return $this->setData(self::KEY_QTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSalable(bool $value): StockDataInterface
    {
        return $this->setData(self::KEY_SALABLE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSalableQty(float $value): StockDataInterface
    {
        return $this->setData(self::KEY_SALABLE_QTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setWithUnlimitedInventory(bool $value): StockDataInterface
    {
        return $this->setData(self::KEY_WITH_UNLIMITED_INVENTORY, $value);
    }
}
