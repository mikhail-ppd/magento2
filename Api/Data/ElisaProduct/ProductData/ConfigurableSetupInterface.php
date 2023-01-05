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
     * @return \Magento\ConfigurableProduct\Api\Data\OptionInterface[]|null Configurable product options
     */
    public function getProductOptions(): array;

    /**
     * Get labels for used attribute values
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelMapInterface[]
     */
    public function getValueLabelMap(): array;

    /**
     * Set the configurable product options if product is of type 'configurable'
     *
     * @param \Magento\ConfigurableProduct\Api\Data\OptionInterface[] $value
     * @return ConfigurableSetupInterface
     */
    public function setProductOptions(array $value): ConfigurableSetupInterface;

    /**
     * Set labels for used attribute values
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelMapInterface[] $value
     * @return ConfigurableSetupInterface
     */
    public function setValueLabelMap(array $value): ConfigurableSetupInterface;
}
