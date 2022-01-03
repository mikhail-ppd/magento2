<?php
namespace Elisa\ProductApi\Model\ResourceModel\CartRequest;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'rlisa_productsapi_cartrequest_collection';
    protected $_eventObject = 'cartrequest_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Elisa\ProductApi\Model\CartRequest', 'Elisa\ProductApi\Model\ResourceModel\CartRequest');
    }

}
