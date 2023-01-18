<?php

namespace Elisa\ProductApi\Api\Service;

use Elisa\ProductApi\Api\Data\EventInterface;
use Elisa\ProductApi\Exception\ServiceException;

interface GetEventsInterface
{
    /**
     * Get Events
     *
     * @return EventInterface[]
     * @throws ServiceException
     */
    public function execute(): array;
}
