<?php

namespace Elisa\ProductApi\Model\StockDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProductInterface;
use Elisa\ProductApi\Api\StockDataBuilderInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class Composite implements StockDataBuilderInterface
{
    /**
     * @var StockDataBuilderInterface[]
     */
    protected $builders;

    /**
     * @param StockDataBuilderInterface[] $builders
     */
    public function __construct(array $builders = [])
    {
        $this->builders = $builders;
    }
    /**
     * @inheritDoc
     */
    public function execute(ElisaProductInterface $elisaProduct, ProductInterface $product)
    {
        foreach ($this->builders as $builder) {
            if (($builder instanceof StockDataBuilderInterface) === false) {
                continue;
            }

            $builder->execute($elisaProduct, $product);
        }
    }
}
