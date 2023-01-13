<?php

namespace Elisa\ProductApi\Model\ResourceModel\RequestToQuote;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'elisa_productsapi_cartrequest_to_quote_collection';
    protected $_eventObject = 'cartrequest_to_quote_collection';

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \Elisa\ProductApi\Model\RequestToQuote::class,
            \Elisa\ProductApi\Model\ResourceModel\RequestToQuote::class
        );
    }

}
