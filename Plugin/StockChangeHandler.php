<?php
namespace Elisa\ProductApi\Plugin;

use Magento\CatalogInventory\Model\StockManagement;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Catalog\Model\ProductRepository;

class StockChangeHandler
{
    protected $stockItemRepository;

    protected $productRepository;

    public function __construct(
        StockItemRepository $stockItemRepository,
        ProductRepository $productRepository
    ) {
        $this->stockItemRepository = $stockItemRepository;
        $this->productRepository = $productRepository;
    }

    public function beforeRegisterProductsSale(StockManagement $subject, $items, $websiteId)
    {
        if (!empty($items)) {
            foreach ($items as $stockItemId => $qty) {
                $stockItem = $this->stockItemRepository->get($stockItemId);
                $productId = $stockItem->getProductId();
                $product = $this->productRepository->getById($productId);
                $now = new \Datetime();
                $product->setUpdatedAt($now->format('Y-m-d H:i:s'))->save();
            }
        }
        return [$items, $websiteId];
    }
}
