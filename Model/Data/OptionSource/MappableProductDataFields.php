<?php

namespace Elisa\ProductApi\Model\Data\OptionSource;

use Magento\Catalog\Model\Product;

class MappableProductDataFields
{
    public const FIELD_NAME = 'name';
    public const FIELD_RECOMMENDED_RETAIL_PRICE = 'rrp';

    public const FIELDS = [
        self::FIELD_NAME,
        self::FIELD_RECOMMENDED_RETAIL_PRICE
    ];

    /** @var array|null */
    protected $options = null;

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        if ($this->options === null) {
            $this->options = [];

            $this->options[] = [
                'value' => self::FIELD_NAME,
                'label' => __("Name")
            ];

            $this->options[] = [
                'value' => self::FIELD_RECOMMENDED_RETAIL_PRICE,
                'label' => __("Suggested Retail Price")
            ];
        }

        return $this->options;
    }
}
