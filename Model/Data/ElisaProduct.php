<?php

namespace Elisa\ProductApi\Model\Data;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterface;
use Elisa\ProductApi\Api\Data\ElisaProductInterface;
use Magento\Framework\DataObject;

class ElisaProduct extends DataObject implements ElisaProductInterface
{
    private const KEY_CHILDREN = 'children';
    private const KEY_PRODUCT_DATA = 'product_data';
    private const KEY_PRODUCT_ID = 'product_id';
    private const KEY_PRODUCT_TYPE_WITH_QTY = 'product_type_with_qty';
    private const KEY_STOCK_DATA = 'stock_data';
    private const KEY_VALID = 'valid';

    /**
     * @inheritDoc
     */
    public function getChildren(): ?array
    {
        return $this->getData(self::KEY_CHILDREN);
    }

    /**
     * @inheritDoc
     */
    public function getProductData(): ?ProductDataInterface
    {
        return $this->getData(self::KEY_PRODUCT_DATA);
    }

    /**
     * @inheritDoc
     */
    public function getProductId(): int
    {
        return (int)$this->getData(self::KEY_PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function isProductTypeWithQty(): ?bool
    {
        return $this->getData(self::KEY_PRODUCT_TYPE_WITH_QTY);
    }

    /**
     * @inheritDoc
     */
    public function getStockData(): ?StockDataInterface
    {
        return $this->getData(self::KEY_STOCK_DATA);
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return (bool)$this->getData(self::KEY_VALID);
    }

    /**
     * @inheritDoc
     */
    public function setChildren(array $value): ElisaProductInterface
    {
        return $this->setData(self::KEY_CHILDREN, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProductData(ProductDataInterface $value): ElisaProductInterface
    {
        return $this->setData(self::KEY_PRODUCT_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProductId(int $value): ElisaProductInterface
    {
        return $this->setData(self::KEY_PRODUCT_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setProductTypeWithQty(bool $value): ElisaProductInterface
    {
        return $this->setData(self::KEY_PRODUCT_TYPE_WITH_QTY, $value);
    }

    /**
     * @inheritDoc
     */
    public function setStockData(StockDataInterface $value): ElisaProductInterface
    {
        return $this->setData(self::KEY_STOCK_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValid(bool $value): ElisaProductInterface
    {
        return $this->setData(self::KEY_VALID, $value);
    }
}
