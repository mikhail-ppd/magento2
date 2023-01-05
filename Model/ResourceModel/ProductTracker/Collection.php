<?php

namespace Elisa\ProductApi\Model\ResourceModel\ProductTracker;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \Elisa\ProductApi\Model\ProductTracker::KEY_PRODUCT_ID;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \Elisa\ProductApi\Model\ProductTracker::class,
            \Elisa\ProductApi\Model\ResourceModel\ProductTracker::class
        );
    }

}
