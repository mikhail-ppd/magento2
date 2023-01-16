<?php

namespace Elisa\ProductApi\Model\ResourceModel\Event;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = \Elisa\ProductApi\Model\Event::KEY_EVENT_ID;

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \Elisa\ProductApi\Model\Event::class,
            \Elisa\ProductApi\Model\ResourceModel\Event::class
        );
    }

}
