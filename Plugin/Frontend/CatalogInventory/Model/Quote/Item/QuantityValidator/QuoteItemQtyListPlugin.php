<?php

namespace Elisa\ProductApi\Plugin\Frontend\CatalogInventory\Model\Quote\Item\QuantityValidator;

use Elisa\ProductApi\Api\CartManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

class QuoteItemQtyListPlugin
{
    /** @var CartManagementInterface */
    protected $cartManagement;
    /** @var int[] */
    protected $randomQuoteIdMap = [];

    /**
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        CartManagementInterface $cartManagement
    ) {
        $this->cartManagement = $cartManagement;
    }

    /**
     * This is due to a "bug" in the subject.
     * Magento does not expect that the quote is emptied and refilled with items in the same request.
     * Some functionality for validating stock had cached the quantity already.
     * Worked around the bug by randomizing the quote ID to a new value if it's a quote that is being
     * recreated from Elisa
     *
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList $subject
     * @param int $productId
     * @param int $quoteItemId
     * @param int $quoteId
     * @param float $itemQty
     * @return array
     */
    public function beforeGetQty($subject, $productId, $quoteItemId, $quoteId, $itemQty): array
    {
        if ($quoteId) {
            $quoteId = (int)$quoteId;

            if ($this->cartManagement->isQuoteProcessing((int)$quoteId)) {
                if (!isset($this->randomQuoteIdMap[$quoteId])) {
                    try {
                        $this->randomQuoteIdMap[$quoteId] = Random::getRandomNumber(time(), time() * 10)
                            . '' . (int)$quoteId;
                    } catch (LocalizedException $e) {
                        $this->randomQuoteIdMap[$quoteId] = time() . (int)$quoteId;
                    }
                }

                $quoteId = $this->randomQuoteIdMap[$quoteId];
            }
        }

        return [$productId, $quoteItemId, $quoteId, $itemQty];
    }
}
