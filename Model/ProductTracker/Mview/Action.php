<?php

namespace Elisa\ProductApi\Model\ProductTracker\Mview;

use Elisa\ProductApi\Api\ParentProductIdProviderInterface;
use Elisa\ProductApi\Model\ResourceModel\ProductTracker as ProductTrackerResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Action implements \Magento\Framework\Mview\ActionInterface
{
    /** @var DateTimeFactory */
    protected $dateTimeFactory;
    /** @var LoggerInterface  */
    protected $logger;
    /** @var ParentProductIdProviderInterface  */
    protected $parentProductIdProvider;
    /** @var ProductTrackerResource */
    protected $productTrackerResource;

    /**
     * @param ProductTrackerResource $productTrackerResource
     * @param DateTimeFactory $dateTimeFactory
     * @param ParentProductIdProviderInterface $parentProductIdProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductTrackerResource $productTrackerResource,
        DateTimeFactory $dateTimeFactory,
        ParentProductIdProviderInterface $parentProductIdProvider,
        LoggerInterface $logger
    ) {
        $this->dateTimeFactory = $dateTimeFactory;
        $this->logger = $logger;
        $this->productTrackerResource = $productTrackerResource;
        $this->parentProductIdProvider = $parentProductIdProvider;
    }

    /**
     * Process mview CL IDs
     *
     * @param int[] $ids
     * @return void
     * @throws \Throwable
     */
    public function execute($ids)
    {
        $timestamp = $this->dateTimeFactory->create()->gmtTimestamp();

        try {
            if ($parentIds = $this->parentProductIdProvider->execute($ids)) {
                $ids = array_unique(array_merge($ids, $parentIds));
            }
            $this->productTrackerResource->updateTrackerRecords($ids, $timestamp);
        } catch (\Throwable $e) {
            $this->logger->critical($e);
            throw $e;
        }
    }
}
