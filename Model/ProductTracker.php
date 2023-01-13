<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\ProductTrackerInterface;
use Magento\Framework\Model\AbstractModel;

class ProductTracker extends AbstractModel implements ProductTrackerInterface
{
    public const KEY_PRODUCT_ID = 'product_id';
    public const KEY_UPDATE_UTC_TIMESTAMP = 'updated_utc_timestamp';

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ProductTracker::class);
    }

    /**
     * @inheritDoc
     */
    public function getProductId(): int
    {
        return (int)$this->getDataByKey(self::KEY_PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateUtcTimestamp(): int
    {
        return (int)$this->getDataByKey(self::KEY_PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId(int $productId): ProductTrackerInterface
    {
        return $this->setData(self::KEY_PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function setUpdateUtcTimestamp(int $utcTimestamp): ProductTrackerInterface
    {
        return $this->setData(self::KEY_UPDATE_UTC_TIMESTAMP, $utcTimestamp);
    }
}
