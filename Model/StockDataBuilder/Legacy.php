<?php

namespace Elisa\ProductApi\Model\StockDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProductInterface;
use Elisa\ProductApi\Api\StockDataBuilderInterface;
use Elisa\ProductApi\Model\DataBuilderContext as Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Legacy implements StockDataBuilderInterface
{
    /** @var Context */
    protected $context;
    /** @var StockConfigurationInterface */
    protected $stockConfiguration;
    /** @var StockRegistryInterface */
    protected $stockRegistry;

    /**
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Context $context
     */
    public function __construct(
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Context $context
    ) {
        $this->context = $context;
        $this->stockConfiguration = $stockConfiguration;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @inheritDoc
     */
    public function execute(ElisaProductInterface $elisaProduct, ProductInterface $product)
    {
        if ($this->context->getModuleManager()->isEnabled('Magento_InventoryApi')) {
            return;
        }

        $elisaProduct->setProductTypeWithQty($this->stockConfiguration->isQty($product->getTypeId()));

        $stockData = $this->context->getDataFactory()->getNewElisaStockData();
        $elisaProduct->setStockData($stockData);

        $websiteId = $this->context->getStoreManager()->getWebsite()->getId();
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $websiteId);

        $stockData->setSalable((bool)$stockItem->getIsInStock());

        if ($elisaProduct->isProductTypeWithQty()) {
            $stockData->setQty((float)$stockItem->getQty());
            $stockData->setSalableQty(max((float)$stockItem->getQty() - (float)$stockItem->getMinQty(), 0));

            $stockData->setWithUnlimitedInventory(
                !$stockItem->getManageStock()
                || $stockItem->getBackorders() !== StockItemInterface::BACKORDERS_NO
            );
        }
    }
}
