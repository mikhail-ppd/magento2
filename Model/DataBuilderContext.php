<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Model\Data\Factory as ElisaDataFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

class DataBuilderContext
{
    /** @var Emulation  */
    protected $appEmulation;
    /** @var ElisaDataFactory  */
    protected $dataFactory;
    /** @var ModuleManager  */
    protected $moduleManager;
    protected SearchCriteriaBuilder $searchCriteriaBuilder;
    /** @var StoreManagerInterface  */
    protected $storeManager;

    /**
     * @param ModuleManager $moduleManager
     * @param ElisaDataFactory $dataFactory
     * @param StoreManagerInterface $storeManager
     * @param Emulation $appEmulation
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ModuleManager $moduleManager,
        ElisaDataFactory $dataFactory,
        StoreManagerInterface $storeManager,
        Emulation $appEmulation,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->appEmulation = $appEmulation;
        $this->dataFactory = $dataFactory;
        $this->moduleManager = $moduleManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
    }

    public function getAppEmulation(): Emulation
    {
        return $this->appEmulation;
    }

    public function getDataFactory(): ElisaDataFactory
    {
        return $this->dataFactory;
    }

    public function getModuleManager(): ModuleManager
    {
        return $this->moduleManager;
    }

    public function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        return $this->searchCriteriaBuilder;
    }

    public function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }
}
