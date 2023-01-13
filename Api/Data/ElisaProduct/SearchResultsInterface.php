<?php

namespace Elisa\ProductApi\Api\Data\ElisaProduct;

/**
 * @api
 */
interface SearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Elisa\ProductApi\Api\Data\ElisaProductInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Elisa\ProductApi\Api\Data\ElisaProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
