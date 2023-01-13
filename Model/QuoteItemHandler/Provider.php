<?php

namespace Elisa\ProductApi\Model\QuoteItemHandler;

use Elisa\ProductApi\Api\QuoteItemHandlerInterface;
use Elisa\ProductApi\Api\QuoteItemHandlerProviderInterface;
use Elisa\ProductApi\Api\SupportedProductTypesProviderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Provider implements QuoteItemHandlerProviderInterface
{
    /** @var QuoteItemHandlerInterface[] */
    protected $handlers = [];
    /** @var SupportedProductTypesProviderInterface */
    protected $supportedProductTypesProvider;

    /**
     * @param QuoteItemHandlerInterface[] $handlers
     * @param SupportedProductTypesProviderInterface $supportedProductTypesProvider
     */
    public function __construct(array $handlers, SupportedProductTypesProviderInterface $supportedProductTypesProvider)
    {
        $this->supportedProductTypesProvider = $supportedProductTypesProvider;
        $supportedProductTypeIds = $supportedProductTypesProvider->getTypeIds();

        foreach ($handlers as $handler) {
            if (($handler instanceof QuoteItemHandlerInterface) === false) {
                continue;
            }

            $this->handlers += array_fill_keys(
                array_intersect($supportedProductTypeIds, $handler->getProductTypeIds()),
                $handler
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getHandler(string $typeId): QuoteItemHandlerInterface
    {
        if (!$this->supportedProductTypesProvider->isSupported($typeId)) {
            throw new LocalizedException(__("Unsupported product type %2.", $typeId));
        }

        if (!isset($this->handlers[$typeId])) {
            throw new LocalizedException(__("Quote Item Handler for type %1 not found.", $typeId));
        }

        return $this->handlers[$typeId];
    }
}
