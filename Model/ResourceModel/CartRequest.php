<?php

namespace Elisa\ProductApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class CartRequest extends AbstractDb
{
    protected $_serializableFields = ['cart_data' => [[], []]];

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('elisa_productsapi_cartrequest', 'id');
    }
}
