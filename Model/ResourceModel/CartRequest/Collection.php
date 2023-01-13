<?php

namespace Elisa\ProductApi\Model\ResourceModel\CartRequest;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'rlisa_productsapi_cartrequest_collection';
    protected $_eventObject = 'cartrequest_collection';

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \Elisa\ProductApi\Model\CartRequest::class,
            \Elisa\ProductApi\Model\ResourceModel\CartRequest::class
        );
    }

}
