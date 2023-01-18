<?php

namespace Elisa\ProductApi\Api\Data;

/**
 * @api
 */
interface VersionInfoInterface
{
    /**
     * Get Cart API version
     *
     * @return string Semantic Version of the Elisa Cart Integration API
     */
    public function getCartApi(): string;
    /**
     * Get Events API version
     *
     * @return string Semantic Version of the Elisa Cart Integration API
     */
    public function getEventsApi(): string;

    /**
     * Get Order API version
     *
     * @return string Semantic Version of the Elisa Order Integration API
     */
    public function getOrderApi(): string;

    /**
     * Get Product API version
     *
     * @return string Semantic Version of the Elisa Product Integration API
     */
    public function getProductApi(): string;

    /**
     * Set Cart API version
     *
     * @param string $value
     * @return VersionInfoInterface
     */
    public function setCartApi(string $value): VersionInfoInterface;

    /**
     * Set Events API version
     *
     * @param string $value
     * @return VersionInfoInterface
     */
    public function setEventsApi(string $value): VersionInfoInterface;


    /**
     * Set Order API version
     *
     * @param string $value
     * @return VersionInfoInterface
     */
    public function setOrderApi(string $value): VersionInfoInterface;

    /**
     * Set Product API version
     *
     * @param string $value
     * @return VersionInfoInterface
     */
    public function setProductApi(string $value): VersionInfoInterface;
}
