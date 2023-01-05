<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct\ProductData\ConfigurableSetup;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelInterface;
use Magento\Framework\DataObject;

class ValueLabel extends DataObject implements ValueLabelInterface
{
    private const KEY_LABEL = 'label';
    private const KEY_VALUE = 'value';

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
    public function getValue(): int
    {
        return (int)$this->getData(self::KEY_VALUE);
    }

    /**
     * @inheritDoc
     */
    public function setLabel(string $value): ValueLabelInterface
    {
        return $this->setData(self::KEY_LABEL, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValue(int $value): ValueLabelInterface
    {
        return $this->setData(self::KEY_VALUE, $value);
    }
}
