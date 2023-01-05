<?php

namespace Elisa\ProductApi\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class CartRequest extends AbstractModel
{
    protected $_cacheTag = 'elisa_productsapi_cartrequest';
    protected $_eventPrefix = 'elisa_productsapi_cartrequest';

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CartRequest::class);
    }
}
