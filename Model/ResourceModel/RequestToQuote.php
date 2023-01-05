<?php
namespace Elisa\ProductApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RequestToQuote extends AbstractDb
{
    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('elisa_productsapi_cartrequest_to_quote', 'id');
    }
}
