<?php

namespace Elisa\ProductApi\Model\Data\OptionSource;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;

class ProductAttributes implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var Config */
    protected $eavConfig;
    /** @var array|null  */
    protected $options = null;

    /**
     * @param Config $eavConfig
     */
    public function __construct(Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];
            $eavAttributes = $this->eavConfig->getEntityAttributes(Product::ENTITY);

            foreach ($eavAttributes as $eavAttribute) {
                /** @var \Magento\Catalog\Model\Entity\Attribute $eavAttribute */
                $attributeCode = $eavAttribute->getAttributeCode();
                $attributeLabel = $eavAttribute->getDefaultFrontendLabel();

                if (!$attributeLabel) {
                    continue;
                }

                $this->options[] = [
                    'value' => $attributeCode,
                    'label' => $attributeLabel . " / {$attributeCode}"
                ];
            }
        }

        return $this->options;
    }
}
