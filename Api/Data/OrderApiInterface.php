<?php

namespace Elisa\ProductApi\Api\Data;

interface OrderApiInterface
{
    /**
     * @param string $timestamp
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function getOrders($timestamp);
}
