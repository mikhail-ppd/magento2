<?php

namespace Elisa\ProductApi\Api;

/**
 * @api
 */
interface OrderManagementInterface
{
    /**
     * Adds Elisa Reference ID to Order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function addElisaReferenceId(\Magento\Sales\Api\Data\OrderInterface $order);

    /**
     * Get list of Orders with Elisa origin
     *
     * @param int $timestamp
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getList(int $timestamp): array;

    /**
     * Checks whether Order originates from Elisa reservation
     *
     * @param int $orderId
     * @return bool
     */
    public function isOrderFromElisa(int $orderId): bool;
}
