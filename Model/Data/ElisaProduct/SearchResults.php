<?php

namespace Elisa\ProductApi\Model\Data\ElisaProduct;

use Elisa\ProductApi\Api\Data\ElisaProduct\SearchResultsInterface;

class SearchResults extends \Magento\Framework\Api\SearchResults implements SearchResultsInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return parent::getItems();
    }

    /**
     * @inheritDoc
     */
    public function setItems(array $items)
    {
        return parent::setItems($items);
    }
}
