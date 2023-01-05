<?php

namespace Elisa\ProductApi\Model\ProductDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Elisa\ProductApi\Api\ProductDataBuilderInterface;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Composite implements ProductDataBuilderInterface
{
    /**
     * @var ProductDataBuilderInterface[]
     */
    protected array $builders;

    /**
     * @param ProductDataBuilderInterface[] $builders
     */
    public function __construct(array $builders = [])
    {
        $this->builders = $builders;
    }

    /**
     * @inheritDoc
     */
    public function execute(
        ProductDataInterface $productData,
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): ?array {
        $resultChildProducts = [];

        foreach ($this->builders as $builder) {
            if (($builder instanceof ProductDataBuilderInterface) === false) {
                continue;
            }

            $results = $builder->execute($productData, $product, $parentProduct);

            if ($results) {
                $resultChildProducts[] = $results;
            }
        }

        return $resultChildProducts ? array_merge(...$resultChildProducts) : null;
    }
}
