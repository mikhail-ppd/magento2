<?php

namespace Elisa\ProductApi\Api;

use Elisa\ProductApi\Model\CartRequest;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;

/**
 * @api
 */
interface CartManagementInterface
{
    /**
     * Get cart creation URL for selected Elisa Products
     *
     * @param mixed $params
     * @return string
     * @throws LocalizedException
     */
    public function getCreateUrl($params): string;

    /**
     * Get Cart Request for given token
     *
     * @param string $token
     * @return CartRequest
     * @throws NoSuchEntityException
     */
    public function getCartRequestFromToken(string $token): CartRequest;

    /**
     * Get Quote for Cart Request
     *
     * @param CartRequest $cartRequest
     * @param \Magento\Quote\Model\Quote $quote
     * @return Quote
     * @throws LocalizedException
     */
    public function setCartRequestToQuote(CartRequest $cartRequest, Quote $quote): Quote;
}
