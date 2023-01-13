<?php

namespace Elisa\ProductApi\Model\Cron;

use Elisa\ProductApi\Model\ResourceModel\ProductTracker as ProductTrackerResource;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class ProductTracker
{
    /** @var DateTimeFactory  */
    protected $dateTimeFactory;
    /** @var ProductTrackerResource  */
    protected $productTrackerResource;

    /**
     * @param ProductTrackerResource $productTrackerResource
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        ProductTrackerResource $productTrackerResource,
        DateTimeFactory $dateTimeFactory
    ) {
        $this->dateTimeFactory = $dateTimeFactory;
        $this->productTrackerResource = $productTrackerResource;
    }

    public function clearOldTrackerData()
    {
        $timestamp = $this->dateTimeFactory->create()->gmtTimestamp();
        $threshold = 259200;//3 days
        $this->productTrackerResource->clearTrackerData($timestamp - $threshold);
    }
}
