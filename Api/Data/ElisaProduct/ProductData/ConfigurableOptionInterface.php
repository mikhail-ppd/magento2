<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct\ProductData;

/**
 * @api
 */
interface ConfigurableOptionInterface
{
    /**
     * Get Attribute ID
     *
     * @return int Attribute ID
     */
    public function getId(): int;

    /**
     * Get Attribute Value Label
     *
     * @return string Attribute Value Label
     */
    public function getValue(): string;

    /**
     * Get Attribute Value ID
     *
     * @return int Attribute Value ID
     */
    public function getValueId(): int;

    /**
     * Set Attribute ID
     *
     * @param int $attributeId
     * @return ConfigurableOptionInterface
     */
    public function setId(int $attributeId): ConfigurableOptionInterface;

    /**
     * Set Attribute Value Label
     *
     * @param string $value
     * @return ConfigurableOptionInterface
     */
    public function setValue(string $value): ConfigurableOptionInterface;

    /**
     * Set Attribute Value Label
     *
     * @param int $value
     * @return ConfigurableOptionInterface
     */
    public function setValueId(int $value): ConfigurableOptionInterface;
}
