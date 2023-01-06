<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup;

/**
 * @api
 */
interface OptionInterface
{
    /**
     * Get Attribute Id
     *
     * @return int
     */
    public function getAttributeId(): int;

    /**
     * Get Label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Get Position of Option
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Get Option Super Attribute ID
     *
     * @return int
     */
    public function getSuperAttributeId(): int;

    /**
     * Get option values
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionValueInterface[]
     */
    public function getValues(): array;

    /**
     * Set Attribute ID
     *
     * @param int $value
     * @return OptionInterface
     */
    public function setAttributeId(int $value): OptionInterface;

    /**
     * Set Label
     *
     * @param string $value
     * @return OptionInterface
     */
    public function setLabel(string $value): OptionInterface;

    /**
     * Set position of Option
     *
     * @param int $value
     * @return OptionInterface
     */
    public function setPosition(int $value): OptionInterface;

    /**
     * Set Option Super Attribute ID
     *
     * @param int $value
     * @return OptionInterface
     */
    public function setSuperAttributeId(int $value): OptionInterface;

    /**
     * Set option values
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionValueInterface[] $value
     * @return OptionInterface
     */
    public function setValues(array $value): OptionInterface;
}
