<?php

namespace Elisa\ProductApi\Api\Data;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CartApiInterface
{
    /**
     * @param mixed $params
     * @return string
     */
    public function cartCreate($params);
}
