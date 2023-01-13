<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\SupportedProductTypesProviderInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SupportedProductTypesProvider implements SupportedProductTypesProviderInterface
{
    /** @var string[] */
    protected $supportedProductTypeIds;

    /**
     * @param string[] $supportedProductTypeIds
     */
    public function __construct(array $supportedProductTypeIds)
    {
        $this->supportedProductTypeIds = array_filter(array_values($supportedProductTypeIds));
    }

    /**
     * @inheritDoc
     */
    public function getTypeIds(): array
    {
        return $this->supportedProductTypeIds;
    }

    /**
     * @inheritDoc
     */
    public function isSupported(string $typeId): bool
    {
        return in_array($typeId, $this->supportedProductTypeIds);
    }
}
