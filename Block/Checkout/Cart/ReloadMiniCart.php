<?php

namespace Elisa\ProductApi\Block\Checkout\Cart;

/**
 * Why this and not etc/frontend/sections.xml targeting "cartassign/index/index"?
 * That action is merely a redirect and will never trigger the customer-data section update on frontend
 */
class ReloadMiniCart extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Elisa_ProductApi::checkout/cart/reload-mini-cart.phtml';

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (!$this->getRequest()->getParam('e-ref')) {
            return '';
        }

        return parent::_toHtml();
    }
}
