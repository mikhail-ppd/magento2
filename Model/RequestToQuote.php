<?php

namespace Elisa\ProductApi\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class RequestToQuote extends AbstractModel
{
    protected $_cacheTag = 'elisa_productsapi_cartrequest_to_quote';
    protected $_eventPrefix = 'elisa_productsapi_cartrequest_to_quote';

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\RequestToQuote::class);
    }
}
