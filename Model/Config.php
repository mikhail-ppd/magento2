<?php

namespace Elisa\ProductApi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
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
     * Returns unserialized config value
     *
     * @param string $path
     * @param int|null $storeId
     *
     * @return mixed
     */
    private function getUnserializedValue(string $path, ?int $storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if (!$value) {
            return null;
        }

        try {
            return $this->serializer->unserialize((string)$value);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
