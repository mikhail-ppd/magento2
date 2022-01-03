<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\ProductApiInterface;
use Magento\Bundle\Model\Product\Type as ModelProductBundle;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ModelProductConfigurable;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ResourceModelProductConfigurable;
use Magento\Eav\Model\Attribute;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Area;
use Magento\GroupedProduct\Model\Product\Type\Grouped as ModelProductGrouped;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductsCollectionFactory;
use Magento\Catalog\Api\Data\ProductSearchResultsInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Framework\Module\Manager;
use Magento\Framework\App\ResourceConnection;

class ProductApi implements ProductApiInterface
{
    const MAX_PRODUCTS_COUNT = 500;
    /**
     * @var ModelProductBundle
     */
    private $modelProductBundle;
    /**
     * @var ImageHelper
     */
    private $imageHelper;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;
    /**
     * @var ResourceModelProductConfigurable
     */
    private $resourceModelProductConfigurable;
    /**
     * @var Attribute
     */
    private $attributeModel;
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;
    /**
     * @var ModelProductGrouped
     */
    private $modelProductGrouped;
    /**
     * @var Emulation
     */
    private $appEmulation;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ProductsCollectionFactory
     */
    private $productsCollectionFactory;
    /**
     * @var ProductSearchResultsInterfaceFactory
     */
    private $productSearchResultsInterfaceFactory;
    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;
    /**
     * @var ReadExtensions
     */
    private $readExtensions;
    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemsCollectionFactory;
    /**
     * @var Manager
     */
    private $moduleManager;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ModelProductBundle $modelProductBundle,
        ImageHelper $imageHelper,
        ProductRepository $productRepository,
        StockRegistryInterface $stockRegistry,
        ResourceModelProductConfigurable $resourceModelProductConfigurable,
        Attribute $attributeModel,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        Emulation $appEmulation,
        StoreManagerInterface $storeManager,
        ModelProductGrouped $modelProductGrouped,
        ProductsCollectionFactory $productsCollectionFactory,
        ProductSearchResultsInterfaceFactory $productSearchResultsInterfaceFactory,
        JoinProcessorInterface $joinProcessor,
        ReadExtensions $readExtensions,
        OrderItemCollectionFactory $orderItemsCollectionFactory,
        Manager $moduleManager,
        ResourceConnection $resourceConnection
    ) {
        $this->modelProductBundle = $modelProductBundle;
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->resourceModelProductConfigurable = $resourceModelProductConfigurable;
        $this->attributeModel = $attributeModel;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->appEmulation = $appEmulation;
        $this->storeManager = $storeManager;
        $this->modelProductGrouped = $modelProductGrouped;
        $this->productsCollectionFactory = $productsCollectionFactory;
        $this->productSearchResultsInterfaceFactory = $productSearchResultsInterfaceFactory;
        $this->joinProcessor = $joinProcessor;
        $this->readExtensions = $readExtensions;
        $this->orderItemsCollectionFactory = $orderItemsCollectionFactory;
        $this->moduleManager = $moduleManager;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param string $timestamp
     * @param string $page
     * @return ProductSearchResultsInterface
     */
    public function getList($timestamp, $page = 1)
    {
        $data = [];
        if ($timestamp) {
            $updatedAt = date("Y-m-d H:i:s", $timestamp);
        } else {
            $updatedAt = date("Y-m-d H:i:s", 0);
        }

        $productsCollection = $this->productsCollectionFactory->create()
            ->setPageSize(self::MAX_PRODUCTS_COUNT)
            ->setCurPage($page);
        $productIds = [];
        if ($timestamp) {
            $orderItemsCollection = $this->orderItemsCollectionFactory->create();
            $orderItemsCollection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('product_id')->distinct(true);
            $orderItemsCollection->addFieldToFilter('created_at', ['gt' => $updatedAt]);
            $productIds = $orderItemsCollection->getColumnValues('product_id');
            $productsCollection->getSelect()->group('e.entity_id');
        }

        $productsCollection->addAttributeToSelect('*');
        $productsCollection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $productsCollection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        $whereCond = "e.updated_at > '".$updatedAt."' ";

        if (!empty($productIds)) {
            $whereCond .= " OR e.entity_id IN (".implode(',', $productIds).") ";
        }
        $productsCollection->getSelect()
            ->where($whereCond)
            ->group('e.entity_id');

        $this->joinProcessor->process($productsCollection);
        $productsCollection->load();


        $items = $productsCollection->getItems();
        $searchResult = $this->productSearchResultsInterfaceFactory->create();
        $searchResult->setItems($items);
        $productIds = [];
        foreach ($items as $product) {
            $this->readExtensions->execute($product);
            $productIds[] = $product->getId();
        }

        foreach ($items as $product) {
            $productData = $this->getProductData($product);
            if ($product->getTypeId() == ModelProductConfigurable::TYPE_CODE) {
                $productData = $this->getProductConfigurableData($product, $productData);
            } elseif ($product->getTypeId() == ModelProductGrouped::TYPE_CODE) {
                $productData = $this->getProductGroupedData($product, $productData);
            } elseif ($product->getTypeId() == ModelProductBundle::TYPE_CODE) {
                $productData = $this->getProductBundleData($product, $productData);
            }

            $parentConfigurableIds = $this->resourceModelProductConfigurable->getParentIdsByChild($product->getId());
            $parentGroupedIds = $this->modelProductGrouped->getParentIdsByChild($product->getId());
            $parentBundleIds = $this->modelProductBundle->getParentIdsByChild($product->getId());
            $parentIds = array_unique(array_merge($parentConfigurableIds, $parentGroupedIds, $parentBundleIds));
            foreach ($parentIds as $key => $parentId) {
                if (in_array($parentId, $productIds)) {
                    unset($parentIds[$key]);
                }
            }
            if ($parentIds) {
                $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
                $searchCriteriaBuilder->addFilter('entity_id', $parentIds, 'in');
                $searchResultParent = $this->productRepository->getList($searchCriteriaBuilder->create());
                $itemsParent = $searchResultParent->getItems();
                $productData['parent_ids'] = $parentIds;
                foreach ($itemsParent as $productParent) {
                    $productParentData = $this->getProductData($productParent);
                    if ($productParent->getTypeId() == ModelProductConfigurable::TYPE_CODE) {
                        $productParentData = $this->getProductConfigurableData($productParent, $productParentData);
                    } elseif ($productParent->getTypeId() == ModelProductGrouped::TYPE_CODE) {
                        $productParentData = $this->getProductGroupedData($productParent, $productParentData);
                    } elseif ($productParent->getTypeId() == ModelProductBundle::TYPE_CODE) {
                        $productParentData = $this->getProductBundleData($productParent, $productParentData);
                    }
                    $data[] = $productParentData;
                    $productIds[] = $productParent->getId();
                }
                $searchResult->setTotalCount($searchResult->getTotalCount() + $searchResultParent->getTotalCount());
            }
            $data[] = $productData;
        }

        $searchResult->setItems($data);
        return $searchResult;
    }

    public function getProductData(ProductInterface $product)
    {
        $productData = [
            'entity_id' => $product->getId(),
            'sku' => $product->getSku(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'url' => $product->getProductUrl(),
            'type_id' => $product->getTypeId(),
            'status' => $product->getAttributeText('status'),
            'visibility' => $product->getAttributeText('visibility')
        ];

        $storeId = $this->storeManager->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $productData['main_image'] = $this->imageHelper->init($product, 'product_page_image_large')->getUrl();
        $this->appEmulation->stopEnvironmentEmulation();

        $productImages = $product->getMediaGalleryImages();
        foreach ($productImages as $productImage) {
            $productData['other_images'][] = $productImage->getUrl();
        }
        $stock = $this->stockRegistry->getStockItem($product->getId());
        $productData['stock'] = $stock->getData();

        if ($this->moduleManager->isEnabled('Magento_InventoryApi')) {
            $connection  = $this->resourceConnection->getConnection();
            $reservationTable = $connection->getTableName('inventory_reservation');
            $reservationSelect = $connection->select()
                ->from($reservationTable, [ReservationInterface::QUANTITY => 'SUM(' . ReservationInterface::QUANTITY . ')'])
                ->where(ReservationInterface::SKU . ' = ?', $product->getSku());
            $reservations = $connection->fetchOne($reservationSelect);
            
            $productData['stock']['qtyReserved'] = $reservations;

        }

        return $productData;
    }

    public function getProductConfigurableData(ProductInterface $product, array $productData)
    {
        $children = $product->getTypeInstance()->getUsedProducts($product);
        $extensionAttributes = $product->getExtensionAttributes();
        $productData['website_ids'] = $extensionAttributes->getWebsiteIds();
        $configurableOptions = $extensionAttributes->getConfigurableProductOptions();
        $productData['configurable_product_options'] = [];

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $option */
        foreach ($configurableOptions as $option) {

            $valueIndex = [];
            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\OptionValue $value */
            foreach ($option->getOptions() as $value) {
                $valueIndex[] = ['value_index' => $value['label']];
            }
            $productData['configurable_product_options'][] = [
                'id' => $option->getId(),
                'attribute_id' => $option->getAttributeId(),
                'label' => $option->getLabel(),
                'position' => $option->getPosition(),
                'values' => $valueIndex,
                'product_id' => $option->getProductId()
            ];
        }

        /** @var \Magento\Catalog\Model\Product $child */
        foreach ($children as $child) {
            $childItem = $this->getProductData($child);
            foreach ($configurableOptions as $option) {
                $attr = $this->attributeModel->load($option->getAttributeId());
                $childItem['configurable_options'][] = [
                    'id' => $option->getAttributeId(),
                    'value' => $child->getAttributeText($attr->getAttributeCode()),
                ];
            }
            $productData['child_items'][] = $childItem;
        }
        return $productData;
    }

    public function getProductGroupedData(ProductInterface $product, array $productData)
    {
        $children = $product->getTypeInstance()->getAssociatedProducts($product);
        foreach ($children as $child) {
            $childItem = $this->getProductData($child);
            $productData['child_items'][] = $childItem;
        }
        return $productData;
    }

    public function getProductBundleData(ProductInterface $product, array $productData)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        $bundleOptions = $extensionAttributes->getBundleProductOptions();
        foreach ($bundleOptions as $option) {
            $productLinks = $option->getProductLinks();
            $productLinksData = [];
            foreach ($productLinks as $productLink) {
                $productLinksData[] = [
                    'id' => $productLink->getId(),
                    'sku' => $productLink->getSku(),
                    'option_id' => $productLink->getOptionId(),
                    'qty' => $productLink->getQty(),
                    'position' => $productLink->getPosition(),
                    'is_default' => $productLink->getIsDefault(),
                    'price' => $productLink->getPrice(),
                    'price_type' => $productLink->getPriceType(),
                    'can_change_quantity' => $productLink->getCanChangeQuantity(),
                ];
            }
            $productData['bundle_product_options'][] = [
                'option_id' => $option->getOptionId(),
                'title' => $option->getTitle(),
                'required' => $option->getRequired(),
                'type' => $option->getType(),
                'position' => $option->getPosition(),
                'sku' => $option->getSku(),
                'product_links' => $productLinksData,
            ];
        }
        /** @var ModelProductBundle $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $childrenIdsArray = $typeInstance->getChildrenIds($product->getId());
        foreach ($childrenIdsArray as $childrenIds) {
            $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
            $searchCriteriaBuilder->addFilter('entity_id', $childrenIds, 'in');
            $searchResultChildren = $this->productRepository->getList($searchCriteriaBuilder->create());
            $childrenItems = $searchResultChildren->getItems();

            $productChildrenData = [];
            foreach ($childrenItems as $productChildren) {
                $productChildrenData[] = $this->getProductData($productChildren);
            }
            $productData['child_items'][] = $productChildrenData;
        }
        return $productData;
    }
}
