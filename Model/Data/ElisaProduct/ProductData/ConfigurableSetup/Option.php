<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct\ProductData\ConfigurableSetup;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\OptionInterface;
use Magento\Framework\DataObject;

class Option extends DataObject implements OptionInterface
{
    private const KEY_ATTRIBUTE_ID = 'attribute_id';
    private const KEY_LABEL = 'label';
    private const KEY_POSITION = 'position';
    private const KEY_SUPER_ATTRIBUTE_ID = 'super_attribute_id';
    private const KEY_VALUES = 'values';

    /**
     * @inheritDoc
     */
    public function getAttributeId(): int
    {
        return (int)$this->getData(self::KEY_ATTRIBUTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return (string)$this->getData(self::KEY_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return (int)$this->getData(self::KEY_POSITION);
    }

    /**
     * @inheritDoc
     */
    public function getSuperAttributeId(): int
    {
        return (int)$this->getData(self::KEY_SUPER_ATTRIBUTE_ID);
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return (array)$this->getData(self::KEY_VALUES);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeId(int $value): OptionInterface
    {
        return $this->setData(self::KEY_ATTRIBUTE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setLabel(string $value): OptionInterface
    {
        return $this->setData(self::KEY_LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setPosition(int $value): OptionInterface
    {
        return $this->setData(self::KEY_POSITION, $value);
    }

    /**
     * @inheritDoc
     */
    public function setSuperAttributeId(int $value): OptionInterface
    {
        return $this->setData(self::KEY_SUPER_ATTRIBUTE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValues(array $value): OptionInterface
    {
        return $this->setData(self::KEY_VALUES, $value);
    }
}
