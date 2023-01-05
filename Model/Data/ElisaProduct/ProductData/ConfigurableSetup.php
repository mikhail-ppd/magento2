<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct\ProductData;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetupInterface;
use Magento\Framework\DataObject;

class ConfigurableSetup extends DataObject implements ConfigurableSetupInterface
{
    private const KEY_PRODUCT_OPTIONS = 'product_options';
    private const KEY_VALUE_LABEL_MAP = 'value_label_map';

    /**
     * @inheritDoc
     */
    public function getProductOptions(): array
    {
        return (array)$this->getData(self::KEY_PRODUCT_OPTIONS);
    }

    /**
     * @inheritDoc
     */
    public function getValueLabelMap(): array
    {
        return (array)$this->getData(self::KEY_VALUE_LABEL_MAP);
    }

    /**
     * @inheritDoc
     */
    public function setProductOptions(array $value): ConfigurableSetupInterface
    {
        return $this->setData(self::KEY_PRODUCT_OPTIONS, $value);
    }

    /**
     * @inheritDoc
     */
    public function setValueLabelMap(array $value): ConfigurableSetupInterface
    {
        return $this->setData(self::KEY_VALUE_LABEL_MAP, $value);
    }
}
