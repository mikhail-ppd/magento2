<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct\ProductData\ConfigurableSetup;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetup\ValueLabelMapInterface;
use Magento\Framework\DataObject;

class ValueLabelMap extends DataObject implements ValueLabelMapInterface
{
    private const KEY_ATTRIBUTE_ID = 'attribute_id';
    private const KEY_VALUE_LABELS = 'value_labels';

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
    public function getValueLabels(): array
    {
        return (array)$this->getData(self::KEY_VALUE_LABELS);
    }

    /**
     * @inheritDoc
     */
    public function setAttributeId(int $value): ValueLabelMapInterface
    {
        return $this->setData(self::KEY_ATTRIBUTE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValueLabels(array $value): ValueLabelMapInterface
    {
        return $this->setData(self::KEY_VALUE_LABELS, $value);
    }
}
