<?php

namespace Elisa\ProductApi\Api;

use Elisa\ProductApi\Api\Data\VersionInfoInterface;

/**
 * @api
 */
interface IntegrationManagementInterface
{
    /**
     * Get API versions
     *
     * @return \Elisa\ProductApi\Api\Data\VersionInfoInterface API versions
     */
    public function getVersionInfo(): VersionInfoInterface;
}
