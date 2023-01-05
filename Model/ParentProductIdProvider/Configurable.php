<?php

namespace Elisa\ProductApi\Model\ParentProductIdProvider;

use Elisa\ProductApi\Api\ParentProductIdProviderInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Configurable implements ParentProductIdProviderInterface
{
    /** @var ConfigurableProductType  */
    protected $configurableProductType;

    /**
     * @param ConfigurableProductType $configurableProductType
     */
    public function __construct(ConfigurableProductType $configurableProductType)
    {
        $this->configurableProductType = $configurableProductType;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $productIds): array
    {
        return array_map('intval', $this->configurableProductType->getParentIdsByChild($productIds));
    }
}
