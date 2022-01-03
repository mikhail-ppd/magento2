<?php

namespace Elisa\ProductApi\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;

class CustomPrice implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $product = $observer->getEvent()->getData('product');
        $productPrice = $product->getCustomPrice();
        if ($productPrice) {
            $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
            $item->setCustomPrice($productPrice);
            $item->setOriginalCustomPrice($productPrice);
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}
