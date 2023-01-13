<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct\ProductData;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableOptionInterface;
use Magento\Framework\DataObject;

class ConfigurableOption extends DataObject implements ConfigurableOptionInterface
{
    private const KEY_ID = 'id';
    private const KEY_VALUE = 'value';
    private const KEY_VALUE_ID = 'value_id';

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return (int)$this->getData(self::KEY_ID);
    }

    /**
     * @inheritDoc
     */
    public function getValue(): string
    {
        return (string)$this->getData(self::KEY_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function getValueId(): int
    {
        return (int)$this->getData(self::KEY_VALUE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setId(int $attributeId): ConfigurableOptionInterface
    {
        return $this->setData(self::KEY_ID, $attributeId);
    }

    /**
     * @inheritDoc
     */
    public function setValue(string $value): ConfigurableOptionInterface
    {
        return $this->setData(self::KEY_VALUE, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValueId(int $value): ConfigurableOptionInterface
    {
        return $this->setData(self::KEY_VALUE_ID, $value);
    }
}
