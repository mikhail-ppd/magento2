<?php

namespace Elisa\ProductApi\Block\Adminhtml\System\Config\Form\Field;

use Elisa\ProductApi\Model\Data\OptionSource\MappableProductDataFields as MappableFieldOptions;
use Elisa\ProductApi\Model\Data\OptionSource\ProductAttributes as ProductAttributeOptions;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AttributeDataMapping extends AbstractFieldArray
{
    /** @var MappableFieldOptions */
    protected $mappableFieldOptions;
    /** @var ProductAttributeOptions */
    protected $productAttributeOptions;
    /** @var ElementFactory */
    protected $elementFactory;
    /** @var array */
    protected $options;

    /**
     * @param Context $context
     * @param ElementFactory $elementFactory
     * @param MappableFieldOptions $mappableFieldOptions
     * @param ProductAttributeOptions $productAttributeOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        ElementFactory $elementFactory,
        MappableFieldOptions $mappableFieldOptions,
        ProductAttributeOptions $productAttributeOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->mappableFieldOptions = $mappableFieldOptions;
        $this->productAttributeOptions = $productAttributeOptions;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->addColumn(
            'field',
            [
                'label' => __('Field'),
            ]
        );

        $this->addColumn(
            'attribute_code',
            [
                'label' => __('Attribute')
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Line');

        parent::_construct();
    }

    public function getCellInputElementId(string $rowId, string $columnName): string
    {
        return $this->_getCellInputElementId($rowId, $columnName);
    }

    public function getCellInputElementName(string $columnName): string
    {
        return $this->_getCellInputElementName($columnName);
    }

    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'field') {
            return str_replace(
                "\n",
                '',
                $this->getSelectElementHtml($columnName, $this->mappableFieldOptions->toOptionArray())
            );
        } elseif ($columnName == 'attribute_code') {
            return str_replace(
                "\n",
                '',
                $this->getSelectElementHtml($columnName, $this->productAttributeOptions->toOptionArray())
            );
        }

        return parent::renderCellTemplate($columnName);
    }

    private function getSelectElementHtml(string $columnName, array $options)
    {
        $element = $this->elementFactory->create('select');

        $element->setForm(
            $this->getForm()
        )->setName(
            $this->_getCellInputElementName($columnName)
        )->setHtmlId(
            $this->_getCellInputElementId('<%- _id %>', $columnName)
        )->setValues(
            $options
        )->setData('style', 'width:auto');

        return str_replace("\n", '', $element->getElementHtml());
    }
}
