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
     * @return SearchResultsInterface
     */
    public function getDeltaList(int $timestamp): SearchResultsInterface;

    /**
     * Get list of Elisa Products
     *
     * @param int $page
     * @param int $pageSize
     * @return SearchResultsInterface
     */
    public function getList(int $page = 1, int $pageSize = 500): SearchResultsInterface;
}
