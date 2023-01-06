<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup;

/**
 * @api
 */
interface OptionValueInterface
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
     * @return OptionValueInterface
     */
    public function setLabel(string $value): OptionValueInterface;

    /**
     * Set value index
     *
     * @param int $value
     * @return OptionValueInterface
     */
    public function setValue(int $value): OptionValueInterface;
}
