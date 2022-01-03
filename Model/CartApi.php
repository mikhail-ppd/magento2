<?php
namespace Elisa\ProductApi\Model;

use Elisa\ProductApi\Api\Data\CartApiInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Elisa\ProductApi\Model\CartRequest as CartRequestModel;
use Elisa\ProductApi\Model\CartRequestFactory as CartRequestFactory;

class CartApi implements CartApiInterface
{
    const MAX_HASH_LENGHT = 20;

    protected $quoteFactory;

    protected $storeManager;

    protected $productRepository;

    protected $cartRequestModel;

    protected $cartRequestFactory;

    protected $serializer;

    protected $urlBuilder;

    public function __construct(
        QuoteFactory $quoteFactory,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        CartRequestModel $cartRequestModel,
        CartRequestFactory $cartRequestFactory,
        SerializerInterface $serializer,
        UrlInterface $urlBuilder
    ){
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->cartRequestModel = $cartRequestModel;
        $this->cartRequestFactory = $cartRequestFactory;
        $this->serializer = $serializer;
        $this->urlBuilder = $urlBuilder;
    }

    public function cartCreate($params)
    {
        if (isset($params['ref_id'])) {
            $refId = $this->validateRefId($params['ref_id']);
        } else {
            $refId = $this->generateRefId($params);
        }
        $refId = isset($params['ref_id']) ? $params['ref_id'] : $this->generateRefId($params);
        $this->cartRequestModel
            ->setCartData($this->serializer->serialize($params))
            ->setRefId($refId)
            ->setUsages(0)
            ->save();
        return ['url' => $this->urlBuilder->getUrl('cartassign/index/index', ['refId' => $refId])];
    }

    protected function generateRefId($params)
    {
        $date = new \DateTime();
        $timestamp = $date->getTimestamp();
        $paramsHash = md5($this->serializer->serialize($params));
        return substr($paramsHash, 0, self::MAX_HASH_LENGHT).$timestamp;
    }

    protected function validateRefId($refId)
    {
        $existingCartRequest = $this->cartRequestFactory->create()->load($refId, 'ref_id');
        if ($existingCartRequest->getId()) {
            throw new \Exception('RefId is already used.');
        }
        return $refId;
    }
}
