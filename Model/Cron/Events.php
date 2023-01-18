<?php

namespace Elisa\ProductApi\Model\Cron;

use Elisa\ProductApi\Api\EventManagementInterface;
use Elisa\ProductApi\Exception\ElisaException;
use Elisa\ProductApi\Exception\ServiceException;
use Elisa\ProductApi\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Events
{
    /** @var Config  */
    protected $config;
    /** @var EventManagementInterface */
    protected $eventManagement;
    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     * @param EventManagementInterface $eventManagement
     * @param Config $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        EventManagementInterface $eventManagement,
        Config $config
    ) {
        $this->config = $config;
        $this->eventManagement = $eventManagement;
        $this->storeManager = $storeManager;
    }

    /**
     * Refresh cached events
     *
     * @return void
     * @throws ElisaException
     * @throws ServiceException
     * @throws LocalizedException
     */
    public function refresh()
    {
        $seenConfigurations = [];

        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();

            if (!$this->config->isOnSiteEventsActive($storeId)) {
                continue;
            }

            $storeConfigurations = $this->config->getOnSiteApiConfigurationHash($storeId);

            if (in_array($storeConfigurations, $seenConfigurations)) {
                continue;
            }

            $seenConfigurations[] = $storeConfigurations;

            $this->eventManagement->refreshEvents($storeId);
        }
    }
}
