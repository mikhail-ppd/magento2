<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup;

/**
 * @api
 */
interface ValueLabelInterface
{
    /**
     * Get label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get value index
     *
     * @return int
     */
    public function getValue(): int;

    /**
     * Set Label
     *
     * @param string $value
     * @return ValueLabelInterface
     */
    public function setLabel(string $value): ValueLabelInterface;

    /**
     * Set value index
     *
     * @param int $value
     * @return ValueLabelInterface
     */
    public function setValue(int $value): ValueLabelInterface;
}
