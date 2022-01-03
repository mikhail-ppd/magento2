<?php

namespace Elisa\ProductApi\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductApiInterface
{
    /**
     * @param string $timestamp
     * @param string $page
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     */
    public function getList($timestamp, $page = 1);
}
