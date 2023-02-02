<?php

namespace Elisa\ProductApi\Controller\Index;

use Elisa\ProductApi\Api\CartManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreSwitcherInterface;

class Index extends Action implements HttpGetActionInterface
{
    /** @var CartManagementInterface */
    protected $cartManagement;
    /** @var Session */
    protected $checkoutSession;
    /** @var StoreManagerInterface */
    protected $storeManager;
    /** @var StoreSwitcherInterface */
    protected $storeSwitcher;

    /**
     * @param Context $context
     * @param CartManagementInterface $cartManagement
     * @param Session $checkoutSession
     * @param StoreManagerInterface $storeManager
     * @param StoreSwitcherInterface $storeSwitcher
     */
    public function __construct(
        Context $context,
        CartManagementInterface $cartManagement,
        Session $checkoutSession,
        StoreManagerInterface $storeManager,
        StoreSwitcherInterface $storeSwitcher
    ) {
        parent::__construct($context);
        $this->cartManagement = $cartManagement;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->storeSwitcher = $storeSwitcher;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $token = $this->getRequest()->getParam('refId', false);
        $url = null;

        if ($token) {
            try {
                $cartRequest = $this->cartManagement->getCartRequestFromToken($token);
                $quote = $this->checkoutSession->getQuote();
                $quote = $this->cartManagement->setCartRequestToQuote($cartRequest, $quote);

                $adjustmentMessages = array_filter($quote->getData('elisa_adjustment_messages') ?? []);

                foreach ($adjustmentMessages as $adjustmentMessage) {
                    $this->messageManager->addNoticeMessage($adjustmentMessage);
                }

                $this->checkoutSession->setQuoteId($quote->getId());
                $items = $quote->getAllVisibleItems();
                /** @var Item $item */
                $item = end($items);

                if ($item) {
                    $this->checkoutSession->setLastAddedProductId($item->getProductId());
                }

                //phpcs:ignore
                $url = $this->_url->getUrl('checkout/cart') . '?e-ref=' . md5($token);

                $currentStore = $this->storeManager->getStore();
                $targetStore = $quote->getStore();

                if ($currentStore->getCode() !== $targetStore->getCode()) {
                    $url = $this->storeSwitcher->switch($currentStore, $targetStore, $url);
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__("Token not provided."));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        if ($url === null) {
            $url = $this->_url->getUrl('checkout/cart');
        }

        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }
}
