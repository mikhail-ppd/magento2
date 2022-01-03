<?php

namespace Elisa\ProductApi\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Elisa\ProductApi\Model\CartRequestFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action
{
    /**
     * @var CartRequest
     */
    protected $cartRequestFactory;

    public function __construct(
        Context $context,
        CartRequestFactory $cartRequestFactory
    ) {
        $this->cartRequestFactory = $cartRequestFactory;
        return parent::__construct($context);
    }

    /**
     * @return ResultFactory
     */
    public function execute()
    {
        $requestParam = $this->_request->getParam('refId', false);
        if ($requestParam) {
            $cartData = $this->cartRequestFactory->create()->load($requestParam, 'ref_id');
            $cartData->createQuoteFromData();
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('/checkout/cart/index');
        return $resultRedirect;
    }
}
