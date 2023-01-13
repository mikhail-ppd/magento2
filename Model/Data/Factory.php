<?php

namespace Elisa\ProductApi\Model\Data;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableOptionInterface as ConfigurableOption;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableOptionInterfaceFactory as ConfigurableOptionFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionInterface as ConfSetupOption;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionInterfaceFactory as ConfSetupOptionFactory;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionValueInterface as ConfSetupOptionValue;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionValueInterfaceFactory as ConfSetupOptionValueFactory;
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
    /** @var ConfSetupOptionFactory */
    protected $confSetupOptionFactory;
    /** @var ConfSetupOptionValueFactory */
    protected $confSetupOptionValueFactory;
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
     * @param ConfSetupOptionFactory $confSetupOptionFactory
     * @param ConfSetupOptionValueFactory $confSetupOptionValueFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ElisaProductFactory $elisaProductFactory,
        SearchResultsFactory $searchResultsFactory,
        ProductDataFactory $productDataFactory,
        StockDataFactory $stockDataFactory,
        ConfigurableOptionFactory $configurableOptionFactory,
        ConfigurableSetupFactory $configurableSetupFactory,
        ConfSetupOptionFactory $confSetupOptionFactory,
        ConfSetupOptionValueFactory $confSetupOptionValueFactory
    ) {
        $this->confSetupOptionFactory = $confSetupOptionFactory;
        $this->confSetupOptionValueFactory = $confSetupOptionValueFactory;
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
     * Creates new instance of Configurable Setup Option
     *
     * @param array $args
     * @return ConfSetupOption
     */
    public function getNewConfigurableSetupOption(array $args = []): ConfSetupOption
    {
        return $this->confSetupOptionFactory->create($args);
    }

    /**
     * Creates new instance of Configurable Setup Option Value
     *
     * @param array $args
     * @return ConfSetupOptionValue
     */
    public function getNewConfigurableSetupOptionValue(array $args = []): ConfSetupOptionValue
    {
        return $this->confSetupOptionValueFactory->create($args);
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
