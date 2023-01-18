<?php

namespace Elisa\ProductApi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const PATH_ON_SITE_ACTIVE = 'elisa_on_site/general/active';
    private const PATH_ON_SITE_DISABLED_HANDLE_MASKS = 'elisa_on_site/general/disabled_handle_masks';
    private const PATH_ON_SITE_PAGE_UID = 'elisa_on_site/general/page_uid';
    private const PATH_ON_SITE_EVENTS_ACTIVE = 'elisa_on_site/events/active';
    private const PATH_ON_SITE_EVENTS_API_ENDPOINT = 'elisa_on_site/events/api_endpoint';
    private const PATH_ON_SITE_EVENTS_API_TOKEN = 'elisa_on_site/events/api_token';
    private const PATH_PRODUCT_API_DATA_MAPPING = 'elisa_sync/product/data_mapping';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;
    /** @var SerializerInterface */
    protected $serializer;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Get hash of configurations used in events API
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOnSiteApiConfigurationHash(?int $storeId = null): string
    {
        $values = [
            $this->getOnSiteEventsApiEndpoint($storeId),
            $this->getOnSiteEventsApiToken($storeId),
            $this->getOnSitePageUid($storeId)
        ];

        return hash("sha256", implode('|', $values));
    }

    /**
     * Gets layout handle masks for not including the Elisa on-site JS
     * For example, you might certainly not want to have this included on the checkout flow and
     * divert the attention of potential conversions.
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getOnSiteDisabledHandleMasks(?int $storeId = null): array
    {
        $handleMasks = [];

        if ($data = (string)$this->getValue(self::PATH_ON_SITE_DISABLED_HANDLE_MASKS, $storeId)) {
            $data = preg_replace('~\R~u', "\n", trim($data));
            $handleMasks = array_filter(explode("\n", $data));
        }

        return $handleMasks;
    }

    /**
     * Get configured API endpoint
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOnSiteEventsApiEndpoint(?int $storeId = null): string
    {
        return (string)$this->getValue(self::PATH_ON_SITE_EVENTS_API_ENDPOINT, $storeId);
    }

    /**
     * Get configured API endpoint
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOnSiteEventsApiToken(?int $storeId = null): string
    {
        return (string)$this->getValue(self::PATH_ON_SITE_EVENTS_API_TOKEN, $storeId);
    }

    /**
     * Get configured API endpoint
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOnSitePageUid(?int $storeId = null): string
    {
        return (string)$this->getValue(self::PATH_ON_SITE_PAGE_UID, $storeId);
    }

    /**
     * Get configured data mapping
     *
     * @param int|null $storeId
     * @return array
     */
    public function getProductDataMapping(?int $storeId = null): array
    {
        try {
            return $this->getUnserializedValue(
                self::PATH_PRODUCT_API_DATA_MAPPING,
                $storeId
            );
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Gets whether onsite api is active and ready for use
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOnSiteActive(?int $storeId = null): bool
    {
        return $this->getBooleanValue(self::PATH_ON_SITE_ACTIVE, $storeId)
            && $this->getOnSitePageUid($storeId);
    }

    /**
     * Gets whether onsite api is active and ready for use
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isOnSiteEventsActive(?int $storeId = null): bool
    {
        return $this->isOnSiteActive($storeId)
            && $this->getBooleanValue(self::PATH_ON_SITE_EVENTS_ACTIVE, $storeId)
            && $this->getOnSiteEventsApiEndpoint($storeId)
            && $this->getOnSiteEventsApiToken($storeId);
    }

    /**
     * Returns config boolean value for store
     *
     * @param string $path
     * @param int|null $storeId
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    private function getBooleanValue(string $path, ?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Returns unserialized config value
     *
     * @param string $path
     * @param int|null $storeId
     *
     * @return mixed
     */
    private function getUnserializedValue(string $path, ?int $storeId = null)
    {
        $value = $this->getValue($path, $storeId);

        if (!$value) {
            return null;
        }

        try {
            return $this->serializer->unserialize((string)$value);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Returns config value for store
     *
     * @param string $path
     * @param int|null $storeId
     * @return mixed
     */
    private function getValue(string $path, ?int $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
