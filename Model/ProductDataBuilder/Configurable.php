<?php

namespace Elisa\ProductApi\Model\ProductDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableOptionInterface;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductData\ConfigurableSetupInterface;
use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Elisa\ProductApi\Api\ProductDataBuilderInterface;
use Elisa\ProductApi\Model\DataBuilderContext as Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Configurable implements ProductDataBuilderInterface
{
    /** @var Context */
    protected $context;
    /** @var EavConfig */
    protected $eavConfig;

    /**
     * @param Context $context
     * @param EavConfig $eavConfig
     */
    public function __construct(
        Context $context,
        EavConfig $eavConfig
    ) {
        $this->context = $context;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public function execute(
        ProductDataInterface $productData,
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): ?array {
        $matchProduct = $product->getTypeId() === ConfigurableType::TYPE_CODE;
        $matchParent = $parentProduct && $parentProduct->getTypeId() === ConfigurableType::TYPE_CODE;

        if (!($matchProduct xor $matchParent)) {
            return null;
        }

        if (($product instanceof Product) === false
            || ($matchParent && ($parentProduct instanceof Product) === false)) {
            return null;
        }

        if ($matchProduct) {
            $configurableSetup = $this->getConfigurableSetup($product);
            $productData->setConfigurableSetup($configurableSetup);
            return $product->getTypeInstance()->getUsedProducts($product);
        }

        $configurableOptions = $this->getVariantConfigurableOptions($product, $parentProduct);
        $productData->setConfigurableOptions($configurableOptions);

        return null;
    }

    /**
     * Get configurable setup from catalog product
     *
     * @param ProductInterface $product
     * @return ConfigurableSetupInterface
     */
    private function getConfigurableSetup(ProductInterface $product): ConfigurableSetupInterface
    {
        $configurableSetup = $this->context->getDataFactory()->getNewConfigurableSetup();

        $extensionAttributes = $product->getExtensionAttributes();
        $productConfigurableOptions = $extensionAttributes->getConfigurableProductOptions();
        $configurableSetup->setProductOptions($productConfigurableOptions);

        $attributeValueLabelLists = [];

        foreach ($productConfigurableOptions as $option) {
            $attributeValueLabelList = $this->context->getDataFactory()
                ->getNewConfigurableAttributeValueLabelMap();

            $attributeValueLabelList->setAttributeId($option->getAttributeId());

            $valueLabels = [];

            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\OptionValue $value */
            foreach ($option->getOptions() as $value) {
                $valueLabel = $this->context->getDataFactory()->getNewConfigurableAttributeValueLabel();
                $valueLabel->setValue($value['value_index']);
                $valueLabel->setLabel($value['label']);
                $valueLabels[] = $valueLabel;
            }

            $attributeValueLabelList->setValueLabels($valueLabels);

            $attributeValueLabelLists[] = $attributeValueLabelList;
        }

        $configurableSetup->setValueLabelMap($attributeValueLabelLists);

        return $configurableSetup;
    }

    /**
     * Get configurable options for variant
     *
     * @param ProductInterface $variantProduct
     * @param ProductInterface $parentProduct
     * @return ConfigurableOptionInterface[]
     * @throws LocalizedException
     */
    private function getVariantConfigurableOptions(
        ProductInterface $variantProduct,
        ProductInterface $parentProduct
    ): array {
        $extensionAttributes = $parentProduct->getExtensionAttributes();
        $parentProductConfigurableOptions = $extensionAttributes->getConfigurableProductOptions();

        $configurableOptions = [];

        foreach ($parentProductConfigurableOptions as $option) {
            $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $option->getAttributeId());
            $configurableOption = $this->context->getDataFactory()->getNewElisaProductConfigurableOption();
            $configurableOption->setId($option->getAttributeId())
                ->setValue($variantProduct->getAttributeText($attribute->getAttributeCode()))
                ->setValueId($variantProduct->getData($attribute->getAttributeCode()));
            $configurableOptions[] = $configurableOption;
        }

        return $configurableOptions;
    }
}
