<?php

namespace Elisa\ProductApi\Api;

use Elisa\ProductApi\Exception\ElisaException;
use Elisa\ProductApi\Exception\ServiceException;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 */
interface EventManagementInterface
{

    /**
     * Refresh cached events
     *
     * @param int|null $storeId
     * @return void
     * @throws ElisaException
     * @throws ServiceException
     * @throws LocalizedException
     */
    public function refreshEvents(?int $storeId = null);
}
