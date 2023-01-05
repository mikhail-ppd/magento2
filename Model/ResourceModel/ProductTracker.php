<?php

namespace Elisa\ProductApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductTracker extends AbstractDb
{

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            'elisa_productsapi_product_tracker',
            \Elisa\ProductApi\Model\ProductTracker::KEY_PRODUCT_ID
        );
    }

    /**
     * Delete all entries earlier than or equal to the given timestamp
     *
     * @param int $timestamp
     * @return \Zend_Db_Statement_Interface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearTrackerData(int $timestamp)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where(
                \Elisa\ProductApi\Model\ProductTracker::KEY_UPDATE_UTC_TIMESTAMP . ' <= ?',
                $timestamp
            );

        $deleteStatement = $connection->deleteFromSelect($select, $this->getMainTable());

        return $connection->query($deleteStatement);
    }

    /**
     * Update timestamps for given product ids
     *
     * @param array $productIds
     * @param int $timestamp
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateTrackerRecords(array $productIds, int $timestamp): int
    {
        $productIds = array_map('intval', $productIds);

        $data = array_map(function ($productId) use ($timestamp) {
            return [
                \Elisa\ProductApi\Model\ProductTracker::KEY_PRODUCT_ID => $productId,
                \Elisa\ProductApi\Model\ProductTracker::KEY_UPDATE_UTC_TIMESTAMP => $timestamp,
            ];
        }, $productIds);

        $connection = $this->getConnection();

        return $connection->insertOnDuplicate(
            $this->getMainTable(),
            $data,
            [
                \Elisa\ProductApi\Model\ProductTracker::KEY_UPDATE_UTC_TIMESTAMP
            ]
        );
    }
}
