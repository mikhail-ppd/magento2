<?php

namespace Elisa\ProductApi\Model\Data;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableOptionInterface as ConfigurableOption;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableOptionInterfaceFactory as ConfigurableOptionFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelInterface as ConfValueLabel;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelInterfaceFactory as ConfValueLabelFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelMapInterface as ConfValueLabelMap;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelMapInterfaceFactory as ConfValueLabelMapFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetupInterface as ConfigurableSetup;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetupInterfaceFactory as ConfigurableSetupFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface as ProductData;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterfaceFactory as ProductDataFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\SearchResultsInterface as SearchResults;
use Elisa\ProductApi\Api\Data\ElisaProduct\SearchResultsInterfaceFactory as SearchResultsFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterface as StockData;
use Elisa\ProductApi\Api\Data\ElisaProduct\StockDataInterfaceFactory as StockDataFactory;
use Elisa\ProductApi\Api\Data\ElisaProductInterface as ElisaProduct;
use Elisa\ProductApi\Api\Data\ElisaProductInterfaceFactory as ElisaProductFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Factory
{
    /** @var ConfValueLabelFactory */
    protected $confValueLabelFactory;
    /** @var ConfValueLabelMapFactory */
    protected $confValueLabelMapFactory;
    /** @var ConfigurableOptionFactory */
    protected $configurableOptionFactory;
    /** @var ConfigurableSetupFactory */
    protected $configurableSetupFactory;
    /** @var ElisaProductFactory */
    protected $elisaProductFactory;
    /** @var ProductDataFactory */
    protected $productDataFactory;
    /** @var SearchResultsFactory */
    protected $searchResultsFactory;
    /** @var StockDataFactory */
    protected $stockDataFactory;

    /**
     * Constructor for Factory
     *
     * @param ElisaProductFactory $elisaProductFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param ProductDataFactory $productDataFactory
     * @param StockDataFactory $stockDataFactory
     * @param ConfigurableOptionFactory $configurableOptionFactory
     * @param ConfigurableSetupFactory $configurableSetupFactory
     * @param ConfValueLabelMapFactory $confValueLabelMapFactory
     * @param ConfValueLabelFactory $confValueLabelFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ElisaProductFactory $elisaProductFactory,
        SearchResultsFactory $searchResultsFactory,
        ProductDataFactory $productDataFactory,
        StockDataFactory $stockDataFactory,
        ConfigurableOptionFactory $configurableOptionFactory,
        ConfigurableSetupFactory $configurableSetupFactory,
        ConfValueLabelMapFactory $confValueLabelMapFactory,
        ConfValueLabelFactory $confValueLabelFactory
    ) {
        $this->confValueLabelFactory = $confValueLabelFactory;
        $this->confValueLabelMapFactory = $confValueLabelMapFactory;
        $this->configurableOptionFactory = $configurableOptionFactory;
        $this->configurableSetupFactory = $configurableSetupFactory;
        $this->elisaProductFactory = $elisaProductFactory;
        $this->productDataFactory = $productDataFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->stockDataFactory = $stockDataFactory;
    }

    /**
     * Creates new instance of ConfigurableSetup
     *
     * @param array $args
     * @return ConfigurableSetup
     */
    public function getNewConfigurableSetup(array $args = []): ConfigurableSetup
    {
        return $this->configurableSetupFactory->create($args);
    }

    /**
     * Creates new instance of Configurable Attribute Value-Label Map
     *
     * @param array $args
     * @return ConfValueLabelMap
     */
    public function getNewConfigurableAttributeValueLabelMap(array $args = []): ConfValueLabelMap
    {
        return $this->confValueLabelMapFactory->create($args);
    }

    /**
     * Creates new instance of Configurable Attribute Value-Label
     *
     * @param array $args
     * @return ConfValueLabel
     */
    public function getNewConfigurableAttributeValueLabel(array $args = []): ConfValueLabel
    {
        return $this->confValueLabelFactory->create($args);
    }

    /**
     * Creates new instance of ElisaProduct
     *
     * @param array $args
     * @return ElisaProduct
     */
    public function getNewElisaProduct(array $args = []): ElisaProduct
    {
        return $this->elisaProductFactory->create($args);
    }

    /**
     * Creates new instance of ElisaProductData Configurable Option
     *
     * @param array $args
     * @return ConfigurableOption
     */
    public function getNewElisaProductConfigurableOption(array $args = []): ConfigurableOption
    {
        return $this->configurableOptionFactory->create($args);
    }

    /**
     * Creates new instance of ElisaProductData
     *
     * @param array $args
     * @return ProductData
     */
    public function getNewElisaProductData(array $args = []): ProductData
    {
        return $this->productDataFactory->create($args);
    }

    /**
     * Creates new instance of ElisaProduct\SearchResults
     *
     * @param array $args
     * @return SearchResults
     */
    public function getNewElisaProductSearchResults(array $args = []): SearchResults
    {
        return $this->searchResultsFactory->create($args);
    }

    /**
     * Creates new instance of ElisaProduct\Inventory
     *
     * @param array $args
     * @return StockData
     */
    public function getNewElisaStockData(array $args = []): StockData
    {
        return $this->stockDataFactory->create($args);
    }
}
