<?php

namespace Elisa\ProductApi\Plugin\Frontend\Sales\Helper;

use Elisa\ProductApi\Api\OrderManagementInterface;
use Magento\Sales\Helper\Reorder;

class ReorderPlugin
{
    /** @var OrderManagementInterface */
    protected $orderManagement;

    /**
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        OrderManagementInterface $orderManagement
    ) {
        $this->orderManagement = $orderManagement;
    }

    /**
     * Prevent reordering if Elisa order
     *
     * @param Reorder $subject
     * @param bool $result
     * @param int $orderId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanReorder(Reorder $subject, $result, $orderId): bool
    {
        return $result && !$this->orderManagement->isOrderFromElisa($orderId);
    }
}
