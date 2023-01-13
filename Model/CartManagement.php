<?php

namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\CartManagementInterface;
use Elisa\ProductApi\Api\QuoteItemHandlerProviderInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\UrlInterface;
use Elisa\ProductApi\Model\ResourceModel\CartRequest as CartRequestResource;
use Elisa\ProductApi\Model\ResourceModel\RequestToQuote as RequestToQuoteResource;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CartManagement implements CartManagementInterface
{
    private const MAX_HASH_LENGTH = 20;

    /** @var CartRequestFactory */
    protected $cartRequestFactory;
    /** @var CartRequestResource */
    protected $cartRequestResource;
    /** @var DataObjectFactory */
    protected $dataObjectFactory;
    /** @var DateTimeFactory */
    protected $dateTimeFactory;
    /** @var LoggerInterface */
    protected $logger;
    /** @var ?int */
    protected $processingQuoteId = null;
    /** @var ProductRepositoryInterface */
    protected $productRepository;
    /** @var QuoteItemHandlerProviderInterface */
    protected $quoteItemHandlerProvider;
    /** @var CartRepositoryInterface */
    protected $quoteRepository;
    /** @var RequestToQuoteFactory */
    protected $requestToQuoteFactory;
    /** @var RequestToQuoteResource */
    protected $requestToQuoteResource;
    /** @var SerializerInterface */
    protected $serializer;
    /** @var StoreManagerInterface  */
    protected $storeManager;
    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @param CartRequestFactory $cartRequestFactory
     * @param CartRequestResource $cartRequestResource
     * @param SerializerInterface $serializer
     * @param UrlInterface $urlBuilder
     * @param DateTimeFactory $dateTimeFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param ProductRepositoryInterface $productRepository
     * @param QuoteItemHandlerProviderInterface $quoteItemHandlerProvider
     * @param RequestToQuoteFactory $requestToQuoteFactory
     * @param RequestToQuoteResource $requestToQuoteResource
     * @param CartRepositoryInterface $quoteRepository
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CartRequestFactory $cartRequestFactory,
        CartRequestResource $cartRequestResource,
        SerializerInterface $serializer,
        UrlInterface $urlBuilder,
        DateTimeFactory $dateTimeFactory,
        DataObjectFactory $dataObjectFactory,
        ProductRepositoryInterface $productRepository,
        QuoteItemHandlerProviderInterface $quoteItemHandlerProvider,
        RequestToQuoteFactory $requestToQuoteFactory,
        RequestToQuoteResource $requestToQuoteResource,
        CartRepositoryInterface $quoteRepository,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->cartRequestFactory = $cartRequestFactory;
        $this->cartRequestResource = $cartRequestResource;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->quoteItemHandlerProvider = $quoteItemHandlerProvider;
        $this->quoteRepository = $quoteRepository;
        $this->requestToQuoteFactory = $requestToQuoteFactory;
        $this->requestToQuoteResource = $requestToQuoteResource;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getCreateUrl($params): string
    {
        if (isset($params['ref_id'])) {
            $this->validateToken($params['ref_id']);
        }

        $refId = $params['ref_id'] ?? $this->getRandomToken($params);

        $cartRequest = $this->cartRequestFactory->create();

        $cartRequest->setCartData($params)
            ->setRefId($refId)
            ->setUsages(0);

        $this->cartRequestResource->save($cartRequest);

        return $this->urlBuilder->getUrl('cartassign', ['refId' => $refId]);
    }

    /**
     * @inheritdoc
     */
    public function getCartRequestFromToken(string $token): CartRequest
    {
        $cartRequest = $this->cartRequestFactory->create();
        $this->cartRequestResource->load($cartRequest, $token, 'ref_id');

        if (!$cartRequest->getId()) {
            throw new NoSuchEntityException(__("No cart found for given token."));
        }

        return $cartRequest;
    }

    /**
     * @inheritDoc
     */
    public function isQuoteProcessing(int $quoteId): bool
    {
        return $this->processingQuoteId === $quoteId;
    }

    /**
     * @inheritdoc
     */
    public function setCartRequestToQuote(CartRequest $cartRequest, Quote $quote): Quote
    {
        $productData = $cartRequest->getDataByPath('cart_data/products');

        if ($productData) {
            if ($quote->getAllItems()) {
                $quote->removeAllItems();

                if ($quoteId = $quote->getId()) {
                    $this->processingQuoteId = (int)$quoteId;
                }
            }

            if ($storeCode = $cartRequest->getData('store_code')) {
                $storeId = $this->storeManager->getStore($storeCode)->getId();
                $quote->setStoreId($storeId);
            }

            foreach ($productData as $productDatum) {
                $requestProduct = $this->dataObjectFactory->create(['data' => $productDatum]);
                $sku = $requestProduct->getData('sku');
                $product = $this->productRepository->get($sku, false, $quote->getStoreId(), true);

                $typeId = $product->getTypeId();

                try {
                    $handler = $this->quoteItemHandlerProvider->getHandler($typeId);
                } catch (LocalizedException $e) {
                    $this->logger->critical(__("Product %1 cannot be added to cart.", $sku));
                    $this->logger->critical($e);

                    throw new LocalizedException(__("Product %1 cannot be added to cart.", $sku), $e);
                }

                try {
                    $buyRequest = $handler->getBuyRequest($product, $requestProduct);
                } catch (LocalizedException $e) {
                    $this->logger->critical($e);
                    throw $e;
                }

                $result = $quote->addProduct($product, $buyRequest);

                if (is_string($result)) {
                    throw new LocalizedException(__($result));
                }

                $handler->updateCustomPrice($result, $product, $requestProduct);
            }
        }

        $quote->getBillingAddress();
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);

        $usages = $cartRequest->getUsages();
        $cartRequest->setUsages($usages + 1);
        $this->cartRequestResource->save($cartRequest);

        /** @var RequestToQuote $requestToQuote */
        $requestToQuote = $this->requestToQuoteFactory->create();
        $requestToQuote->setRefId($cartRequest->getId())->setQuoteId($quote->getId());
        $this->requestToQuoteResource->save($requestToQuote);

        return $quote;
    }

    /**
     * Generate token
     *
     * @param mixed $params
     * @return string
     */
    private function getRandomToken($params): string
    {
        $timestamp = $this->dateTimeFactory->create()->gmtTimestamp();
        //phpcs:ignore
        $paramsHash = md5($this->serializer->serialize($params));
        return substr($paramsHash, 0, self::MAX_HASH_LENGTH) . $timestamp;
    }

    /**
     * Validate that the token is not already used
     *
     * @param string $refId
     * @return void
     * @throws LocalizedException
     */
    private function validateToken(string $refId)
    {
        try {
            $this->getCartRequestFromToken($refId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        throw new LocalizedException(__('Token is already used.'));
    }
}
