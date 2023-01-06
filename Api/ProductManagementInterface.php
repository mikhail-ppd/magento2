<?php

namespace Elisa\ProductApi\Api;

use Elisa\ProductApi\Api\Data\ElisaProduct\SearchResultsInterface;

/**
 * @api
 */
interface ProductManagementInterface
{
    /**
     * Get list of changed Elisa Products since given UTC timestamp
     *
     * @param int $timestamp
     * @param string[] $skus Optional list of SKUs to filter list with
     * @return SearchResultsInterface
     */
    public function getDeltaList(int $timestamp, array $skus = []): SearchResultsInterface;

    /**
     * Get list of Elisa Products
     *
     * @param int $page
     * @param int $pageSize
     * @param string[] $skus Optional list of SKUs to filter list with
     * @return SearchResultsInterface
     */
    public function getList(int $page = 1, int $pageSize = 500, array $skus = []): SearchResultsInterface;
}
