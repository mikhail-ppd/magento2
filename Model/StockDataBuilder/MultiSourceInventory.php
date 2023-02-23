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
    /** @var GetProductSalableQtyInterface|null */
    protected $getProductSalableQty;
    /** @var GetStockIdForCurrentWebsite|null */
    protected $getStockIdForCurrentWebsite;
    /** @var GetStockItemConfigurationInterface|null */
    protected $getStockItemConfiguration;
    /** @var GetStockItemDataInterface|null */
    protected $getStockItemData;
    /** @var IsSourceItemManagementAllowedForProductTypeInterface|null */
    protected $isSourceItemManagementAllowedForProductType;

    /**
     * @param Context $context
     * @param GetStockIdForCurrentWebsite|null $getStockIdForCurrentWebsite
     * @param GetStockItemConfigurationInterface|null $getStockItemConfiguration
     * @param GetProductSalableQtyInterface|null $getProductSalableQty
     * @param GetStockItemDataInterface|null $getStockItemData
     * @param IsSourceItemManagementAllowedForProductTypeInterface|null $isSourceItemManagementAllowedForProductType
     */
    public function __construct(
        Context $context,
        ?GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite = null,
        ?GetStockItemConfigurationInterface $getStockItemConfiguration = null,
        ?GetProductSalableQtyInterface $getProductSalableQty = null,
        ?GetStockItemDataInterface $getStockItemData = null,
        ?IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType = null
    ) {
        $this->context = $context;

        if ($this->isMsiAvailable()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $this->getProductSalableQty =
                $getProductSalableQty ?? $objectManager->get(GetProductSalableQtyInterface::class);

            $this->getStockIdForCurrentWebsite =
                $getStockIdForCurrentWebsite ?? $objectManager->get(GetStockIdForCurrentWebsite::class);

            $this->getStockItemConfiguration =
                $getStockItemConfiguration ?? $objectManager->get(GetStockItemConfigurationInterface::class);

            $this->getStockItemData = $getStockItemData ?? $objectManager->get(GetStockItemDataInterface::class);

            $this->isSourceItemManagementAllowedForProductType =
                $isSourceItemManagementAllowedForProductType
                ?? $objectManager->get(IsSourceItemManagementAllowedForProductTypeInterface::class);
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(ElisaProductInterface $elisaProduct, ProductInterface $product)
    {
        if (!$this->isMsiAvailable()) {
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

    /**
     * Check whether MSI is available
     *
     * @return bool
     */
    private function isMsiAvailable(): bool
    {
        return $this->context->getModuleManager()->isEnabled('Magento_InventoryApi');
    }
}
