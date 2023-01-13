<?php

namespace Elisa\ProductApi\Api;

interface SupportedProductTypesProviderInterface
{
    /**
     * Get Supported Type IDs
     *
     * @return string[]
     */
    public function getTypeIds(): array;

    /**
     * Whether provided Type ID is supported
     *
     * @param string $typeId
     * @return bool
     */
    public function isSupported(string $typeId): bool;
}
