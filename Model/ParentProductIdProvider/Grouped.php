<?php

namespace Elisa\ProductApi\Model\ParentProductIdProvider;

use Elisa\ProductApi\Api\ParentProductIdProviderInterface;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;

class Grouped implements ParentProductIdProviderInterface
{
    /** @var GroupedProductType  */
    protected $groupedProductType;

    /**
     * @param GroupedProductType $groupedProductType
     */
    public function __construct(GroupedProductType $groupedProductType)
    {
        $this->groupedProductType = $groupedProductType;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $productIds): array
    {
        return array_map('intval', $this->groupedProductType->getParentIdsByChild($productIds));
    }
}
