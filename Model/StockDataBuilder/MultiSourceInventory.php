<?php

namespace Elisa\ProductApi\Model\StockDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProductInterface;
use Elisa\ProductApi\Api\StockDataBuilderInterface;
use Elisa\ProductApi\Model\DataBuilderContext as Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\GetStockItemDataInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MultiSourceInventory implements StockDataBuilderInterface
{
    /** @var Context */
    protected $context;
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

    /**
     * @param GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param GetStockItemDataInterface $getStockItemData
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param Context $context
     */
    public function __construct(
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        GetProductSalableQtyInterface $getProductSalableQty,
        GetStockItemDataInterface $getStockItemData,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        Context $context
    ) {
        $this->context = $context;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getStockItemData = $getStockItemData;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
    }

    /**
     * @inheritDoc
     */
    public function execute(ElisaProductInterface $elisaProduct, ProductInterface $product)
    {
        if (!$this->context->getModuleManager()->isEnabled('Magento_InventoryApi')) {
            return;
        }

        $elisaProduct->setProductTypeWithQty(
            $this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId())
        );

        $stockData = $this->context->getDataFactory()->getNewElisaStockData();
        $elisaProduct->setStockData($stockData);

        $stockId = $this->getStockIdForCurrentWebsite->execute();

        $stockItemData = $this->getStockItemData->execute($product->getSku(), $stockId);

        $stockData->setSalable(
            $stockItemData ? $stockItemData[GetStockItemDataInterface::IS_SALABLE] : false
        );

        if ($elisaProduct->isProductTypeWithQty()) {
            $stockData->setQty(
                $stockItemData ? $stockItemData[GetStockItemDataInterface::QUANTITY] : 0
            );

            $salableQty = $this->getProductSalableQty->execute($product->getSku(), $stockId);
            $stockData->setSalableQty(max($salableQty, 0));

            $stockItemConfiguration = $this->getStockItemConfiguration->execute($product->getSku(), $stockId);

            $stockData->setWithUnlimitedInventory(
                !$stockItemConfiguration->isManageStock()
                || $stockItemConfiguration->getBackorders() !== StockItemConfigurationInterface::BACKORDERS_NO
            );
        }
    }

}
