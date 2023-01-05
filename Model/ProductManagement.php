<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\ElisaProduct\SearchResultsInterface;
use Elisa\ProductApi\Api\Data\ElisaProductInterface;
use Elisa\ProductApi\Api\ProductDataBuilderInterface;
use Elisa\ProductApi\Api\ProductManagementInterface;
use Elisa\ProductApi\Api\ProductValidatorInterface;
use Elisa\ProductApi\Api\StockDataBuilderInterface;
use Elisa\ProductApi\Api\SupportedProductTypesProviderInterface;
use Elisa\ProductApi\Model\Data\Factory as ElisaDataFactory;
use Elisa\ProductApi\Model\ResourceModel\ProductTracker\Collection as ProductTrackerCollection;
use Elisa\ProductApi\Model\ResourceModel\ProductTracker\CollectionFactory as ProductTrackerCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ProductManagement implements ProductManagementInterface
{
    /** @var Status */
    protected $catalogProductStatus;
    /** @var Visibility */
    protected $catalogProductVisibility;
    /** @var ElisaDataFactory */
    protected $dataFactory;
    /** @var LoggerInterface */
    protected $logger;
    /** @var ProductDataBuilderInterface */
    protected $productDataBuilder;
    /** @var ProductRepositoryInterface */
    protected $productRepository;
    /** @var ProductTrackerCollectionFactory */
    protected $productTrackerCollectionFactory;
    /** @var ProductValidatorInterface */
    protected $productValidator;
    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;
    /** @var StockDataBuilderInterface */
    protected $stockDataBuilder;
    /** @var int[] */
    protected $processedProductIds = [];
    /** @var SupportedProductTypesProviderInterface */
    protected $supportedProductTypesProvider;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param ProductTrackerCollectionFactory $productTrackerCollectionFactory
     * @param ElisaDataFactory $dataFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductDataBuilderInterface $productDataBuilder
     * @param StockDataBuilderInterface $stockDataBuilder
     * @param ProductValidatorInterface $productValidator
     * @param Status $catalogProductStatus
     * @param Visibility $catalogProductVisibility
     * @param LoggerInterface $logger
     * @param SupportedProductTypesProviderInterface $supportedProductTypesProvider
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductTrackerCollectionFactory $productTrackerCollectionFactory,
        ElisaDataFactory $dataFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductDataBuilderInterface $productDataBuilder,
        StockDataBuilderInterface $stockDataBuilder,
        ProductValidatorInterface $productValidator,
        Status $catalogProductStatus,
        Visibility $catalogProductVisibility,
        LoggerInterface $logger,
        SupportedProductTypesProviderInterface $supportedProductTypesProvider
    ) {
        $this->catalogProductStatus = $catalogProductStatus;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->dataFactory = $dataFactory;
        $this->logger = $logger;
        $this->productDataBuilder = $productDataBuilder;
        $this->productRepository = $productRepository;
        $this->productTrackerCollectionFactory = $productTrackerCollectionFactory;
        $this->productValidator = $productValidator;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockDataBuilder = $stockDataBuilder;
        $this->supportedProductTypesProvider = $supportedProductTypesProvider;
    }

    /**
     * @inheritDoc
     */
    public function getDeltaList(int $timestamp): SearchResultsInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;

        /** @var ProductTrackerCollection $trackerCollection */
        $trackerCollection = $this->productTrackerCollectionFactory->create();
        $trackerCollection->addFieldToFilter(ProductTracker::KEY_UPDATE_UTC_TIMESTAMP, ['gt' => $timestamp]);
        $productIds = array_map('intval', $trackerCollection->getAllIds());
        $searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in');
        $searchCriteriaBuilder->addFilter('type_id', $this->supportedProductTypesProvider->getTypeIds(), 'in');

        return $this->getListResults($searchCriteriaBuilder->create(), $productIds);
    }

    /**
     * @inheritdoc
     */
    public function getList(int $page = 1, int $pageSize = 500): SearchResultsInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;

        $searchCriteriaBuilder->addFilter(
            ProductInterface::VISIBILITY,
            $this->catalogProductVisibility->getVisibleInSiteIds(),
            'in'
        );

        $searchCriteriaBuilder->addFilter(
            ProductInterface::STATUS,
            $this->catalogProductStatus->getVisibleStatusIds(),
            'in'
        );

        $searchCriteriaBuilder->addFilter('type_id', $this->supportedProductTypesProvider->getTypeIds(), 'in');

        $searchCriteriaBuilder->setPageSize($pageSize)->setCurrentPage($page);
        return $this->getListResults($searchCriteriaBuilder->create());
    }

    /**
     * Returns list of elisa products based on search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @param array $changedProductIds
     * @return SearchResultsInterface
     */
    private function getListResults(
        SearchCriteria $searchCriteria,
        array $changedProductIds = []
    ): SearchResultsInterface {
        $collectionProductIds = [];

        $searchResult = $this->productRepository->getList($searchCriteria);
        $items = $searchResult->getItems();

        $elisaProducts = [];
        foreach ($items as $product) {
            $collectionProductIds[] = (int)$product->getId();

            try {
                $elisaProducts[] = $this->processCatalogProduct($product);
            } catch (LocalizedException $e) {
                $this->logger->error($e);
            }
        }

        if ($changedProductIds) {
            $notFoundProductIds = array_diff($changedProductIds, $collectionProductIds);

            foreach ($notFoundProductIds as $notFoundProductId) {
                $elisaProduct = $this->dataFactory->getNewElisaProduct();
                $elisaProduct->setProductId($notFoundProductId);
                $elisaProduct->setValid(false);
                $elisaProducts[] = $elisaProduct;
            }
        }

        $searchResults = $this->dataFactory->getNewElisaProductSearchResults();
        $searchResults->setItems($elisaProducts);
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($searchResult->getTotalCount());

        return $searchResults;
    }

    /**
     * Convert product to elisa product
     *
     * @param ProductInterface $product
     * @param ProductInterface|null $parentProduct
     * @return ElisaProductInterface
     * @throws LocalizedException
     */
    private function processCatalogProduct(
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): ElisaProductInterface {
        $this->processedProductIds[] = (int)$product->getId();
        $elisaProduct = $this->dataFactory->getNewElisaProduct();
        $elisaProduct->setProductId($product->getId());

        if (!$this->productValidator->execute($product, $parentProduct)) {
            $elisaProduct->setValid(false);
            return $elisaProduct;
        }

        $elisaProduct->setValid(true);

        $productData = $this->dataFactory->getNewElisaProductData();
        $childrenProducts = $this->productDataBuilder->execute($productData, $product, $parentProduct);
        $elisaProduct->setProductData($productData);
        $this->stockDataBuilder->execute($elisaProduct, $product);

        if ($childrenProducts) {
            $childrenElisaProducts = [];

            foreach ($childrenProducts as $childProduct) {
                if ($results = $this->processCatalogProduct($childProduct, $product)) {
                    $childrenElisaProducts[] = $results;
                }
            }

            $elisaProduct->setChildren($childrenElisaProducts);
        }

        return $elisaProduct;
    }
}
