<?php

namespace Elisa\ProductApi\Model\ParentProductIdProvider;

use Elisa\ProductApi\Api\ParentProductIdProviderInterface;

class Composite implements ParentProductIdProviderInterface
{
    /**
     * @var ParentProductIdProviderInterface[]
     */
    protected array $providers;

    /**
     * @param ParentProductIdProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $productIds): array
    {
        $resultParentIds = [];

        foreach ($this->providers as $provider) {
            if (($provider instanceof ParentProductIdProviderInterface) === false) {
                continue;
            }

            $providerIds = $provider->execute($productIds);

            if ($providerIds) {
                $resultParentIds[] = $providerIds;
            }
        }

        return array_unique(array_merge(...$resultParentIds));
    }
}
