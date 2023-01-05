<?php

namespace Elisa\ProductApi\Api\Data;

/**
 * @api
 */
interface ProductTrackerInterface
{
    /**
     * Get Product Entity ID
     *
     * @return int
     */
    public function getProductId(): int;

    /**
     * Get UTC Timestamp at which product was updated
     *
     * @return int
     */
    public function getUpdateUtcTimestamp(): int;

    /**
     * Set Product Entity ID
     *
     * @param int $productId
     * @return ProductTrackerInterface
     */
    public function setProductId(int $productId): ProductTrackerInterface;

    /**
     * SET UTC Timestamp at which product was updated
     *
     * @param int $utcTimestamp
     * @return ProductTrackerInterface
     */
    public function setUpdateUtcTimestamp(int $utcTimestamp): ProductTrackerInterface;
}
