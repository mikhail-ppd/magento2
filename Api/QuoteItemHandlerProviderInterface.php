<?php

namespace Elisa\ProductApi\Api;

use Magento\Framework\Exception\LocalizedException;

interface QuoteItemHandlerProviderInterface
{

    /**
     * Get handler for given Type ID
     *
     * @param string $typeId
     * @return QuoteItemHandlerInterface
     * @throws LocalizedException
     */
    public function getHandler(string $typeId): QuoteItemHandlerInterface;
}
