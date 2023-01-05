<?php

namespace Elisa\ProductApi\Model\ProductValidator;

use Elisa\ProductApi\Api\ProductValidatorInterface;
use Magento\Catalog\Api\Data\ProductInterface;

class Composite implements ProductValidatorInterface
{
    /**
     * @var ProductValidatorInterface[]
     */
    protected array $validators;

    /**
     * @param ProductValidatorInterface[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * @inheritDoc
     */
    public function execute(ProductInterface $product, ?ProductInterface $parentProduct = null): bool
    {
        $valid = true;

        foreach ($this->validators as $validator) {
            if (($validator instanceof ProductValidatorInterface) === false) {
                continue;
            }

            $valid &= $validator->execute($product, $parentProduct);

            if (!$valid) {
                break;
            }
        }

        return $valid;
    }
}
