<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct\ProductData;

/**
 * @api
 */
interface ConfigurableSetupInterface
{
    /**
     * Get the configurable product options if product is of type 'configurable'
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionInterface[]
     */
    public function getProductOptions(): array;

    /**
     * Set the configurable product options if product is of type 'configurable'
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionInterface[] $value
     * @return ConfigurableSetupInterface
     */
    public function setProductOptions(array $value): ConfigurableSetupInterface;
}
