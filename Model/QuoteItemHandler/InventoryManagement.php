<?php

namespace Elisa\ProductApi\Model\QuoteItemHandler;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class InventoryManagement
{
    /** @var GetProductSalableQtyInterface */
    protected $getProductSalableQty;
    /** @var GetStockIdForCurrentWebsite */
    protected $getStockIdForCurrentWebsite;
    /** @var GetStockItemConfigurationInterface */
    protected $getStockItemConfiguration;
    /** @var GetStockItemDataInterface */
    protected $getStockItemData;
    /** @var IsSourceItemManagementAllowedForProductTypeInterface */
    protected $isSourceItemManagementAllowedForProductType;
    /** @var ModuleManager */
    protected $moduleManager;
    /** @var StockConfigurationInterface */
    protected $stockConfiguration;
    /** @var StockRegistryInterface */
    protected $stockRegistry;
    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param GetStockItemDataInterface $getStockItemData
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param ModuleManager $moduleManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        GetProductSalableQtyInterface $getProductSalableQty,
        GetStockItemDataInterface $getStockItemData,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        ModuleManager $moduleManager,
        StoreManagerInterface $storeManager
    ) {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getStockItemData = $getStockItemData;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->moduleManager = $moduleManager;
        $this->stockConfiguration = $stockConfiguration;
        $this->stockRegistry = $stockRegistry;
        $this->storeManager = $storeManager;
    }

    /**
     * Get MineSale Qty
     *
     * @param ProductInterface $product
     * @return float
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMinSaleQty(ProductInterface $product): float
    {
        if ($this->isMsiAvailable()) {
            if (!$this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId())) {
                return 0;
            }

            $stockId = $this->getStockIdForCurrentWebsite->execute();
            $stockItemConfiguration = $this->getStockItemConfiguration->execute($product->getSku(), $stockId);
            return $stockItemConfiguration->getMinSaleQty();
        }

        $websiteId = $this->storeManager->getWebsite()->getId();
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $websiteId);
        return $stockItem->getMinSaleQty();
    }

    /**
     * Get Max Sale Qty
     *
     * @param ProductInterface $product
     * @return float
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMaxSaleQty(ProductInterface $product): float
    {
        if ($this->isMsiAvailable()) {
            if (!$this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId())) {
                return 0;
            }

            $stockId = $this->getStockIdForCurrentWebsite->execute();
            $salableQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);

            $stockItemConfiguration = $this->getStockItemConfiguration->execute($product->getSku(), $stockId);
            $maxSaleQty = $stockItemConfiguration->getMaxSaleQty();

            return min($maxSaleQty, max($salableQty, 0));
        }

        $websiteId = $this->storeManager->getWebsite()->getId();
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $websiteId);
        $maxSaleQty = $stockItem->getMaxSaleQty();
        return min($maxSaleQty, max($stockItem->getQty() - $stockItem->getMinQty(), 0));
    }

    /**
     * Can product be backordered
     *
     * @param ProductInterface $product
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function isBackordersAllowed(ProductInterface $product): bool
    {
        if ($this->isMsiAvailable()) {
            if (!$this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId())) {
                return false;
            }

            $stockId = $this->getStockIdForCurrentWebsite->execute();

            $stockItemConfiguration = $this->getStockItemConfiguration->execute($product->getSku(), $stockId);

            return !$stockItemConfiguration->isManageStock()
                || $stockItemConfiguration->getBackorders() !== StockItemConfigurationInterface::BACKORDERS_NO;
        }

        $websiteId = $this->storeManager->getWebsite()->getId();
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $websiteId);

        return !$stockItem->getManageStock()
            || $stockItem->getBackorders() !== StockItemInterface::BACKORDERS_NO;
    }

    /**
     * Whether item is salable
     *
     * @param ProductInterface $product
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isSalable(ProductInterface $product): bool
    {
        if ($this->isMsiAvailable()) {
            $stockId = $this->getStockIdForCurrentWebsite->execute();
            $stockItemData = $this->getStockItemData->execute($product->getSku(), $stockId);
            return filter_var($stockItemData[GetStockItemDataInterface::IS_SALABLE], FILTER_VALIDATE_BOOLEAN);
        }

        $websiteId = $this->storeManager->getWebsite()->getId();
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $websiteId);
        return filter_var($stockItem->getIsInStock(), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Check whether MSI is available
     *
     * @return bool
     */
    private function isMsiAvailable(): bool
    {
        return $this->moduleManager->isEnabled('Magento_InventoryApi');
    }
}
