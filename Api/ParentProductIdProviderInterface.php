<?php

namespace Elisa\ProductApi\Api;

interface ParentProductIdProviderInterface
{
    /**
     * Get parent product IDS for given product IDs
     *
     * @param int[] $productIds
     * @return int[]
     */
    public function execute(array $productIds): array;
}
