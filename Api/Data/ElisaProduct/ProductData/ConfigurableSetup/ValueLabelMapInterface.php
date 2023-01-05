<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup;

/**
 * @api
 */
interface ValueLabelMapInterface
{
    /**
     * Get attribute ID
     *
     * @return int
     */
    public function getAttributeId(): int;

    /**
     * Get value index labels
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelInterface[]
     */
    public function getValueLabels(): array;

    /**
     * Set attribute ID
     *
     * @param int $value
     * @return ValueLabelMapInterface
     */
    public function setAttributeId(int $value): ValueLabelMapInterface;

    /**
     * Set value index labels
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelInterface[] $value
     * @return ValueLabelMapInterface
     */
    public function setValueLabels(array $value): ValueLabelMapInterface;
}
