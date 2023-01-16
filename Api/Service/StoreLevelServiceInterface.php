<?php

namespace Elisa\ProductApi\Api\Service;

/**
 * @api
 */
interface StoreLevelServiceInterface
{
    /**
     * Get Store ID
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * Set Store ID
     *
     * @param int $storeId
     * @return StoreLevelServiceInterface
     */
    public function setStoreId(int $storeId): StoreLevelServiceInterface;
}
