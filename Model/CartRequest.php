<?php
namespace Elisa\ProductApi\Model;

use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Serialize\SerializerInterface;
use \Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\ManagerInterface;
use Magento\Bundle\Model\Product\Type as ModelProductBundle;
use Magento\GroupedProduct\Model\Product\Type\Grouped as ModelProductGrouped;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ModelProductConfigurable;
use Magento\Catalog\Model\Product\Type as ModelProductSimple;
use function PHPUnit\Framework\throwException;
use Magento\Bundle\Model\Option as BundleOption;
use Magento\Bundle\Model\Product\Type as BundleProductType;
use Magento\Downloadable\Model\Product\Type as DownloadableProductType;

class CartRequest extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'elisa_productsapi_cartrequest';

    protected $_cacheTag = 'elisa_productsapi_cartrequest';

    protected $_eventPrefix = 'elisa_productsapi_cartrequest';

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var RequestToQuote
     */
    protected $requestToQuote;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var BundleOption
     */
    protected $bundleOption;

    /**
     * @var BundleProductType
     */
    protected $bundleProductType;

    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        SerializerInterface $serializer,
        StoreManagerInterface $storeManager,
        ProductRepository $productRepository,
        Cart $cart,
        FormKey $formKey,
        RequestToQuote $requestToQuote,
        ManagerInterface $messageManager,
        BundleOption $bundleOption,
        BundleProductType $bundleProductType,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->cart = $cart;
        $this->formKey = $formKey;
        $this->requestToQuote = $requestToQuote;
        $this->messageManager = $messageManager;
        $this->bundleOption = $bundleOption;
        $this->bundleProductType = $bundleProductType;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_construct();
    }

    protected function _construct()
    {
        $this->_init('Elisa\ProductApi\Model\ResourceModel\CartRequest');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * @return void
     */
    public function createQuoteFromData()
    {
        $requestDataSerialized = $this->getCartData();
        $quoteData = $this->serializer->unserialize($requestDataSerialized);
        if (isset($quoteData['products'])) {
            $store = $this->storeManager->getStore();
            foreach ($quoteData['products'] as $productData) {
                if (isset($productData['sku'])) {
                    try {
                        $product = $this->productRepository->get($productData['sku']);
                        $qty = isset($productData['qty']) ? $productData['qty'] : 1;

                        switch ($product->getTypeId()) {
                            case ModelProductSimple::TYPE_VIRTUAL:
                            case DownloadableProductType::TYPE_DOWNLOADABLE:
                            case ModelProductSimple::TYPE_SIMPLE:
                                $params = array(
                                    'form_key' => $this->formKey->getFormKey(),
                                    'product' => $product->getId(),
                                    'qty'   => $qty
                                );
                                if (isset($productData['price'])) {
                                    $product->setCustomPrice($productData['price']);
                                }
                                $this->cart->addProduct($product, $params);
                                break;
                            case ModelProductConfigurable::TYPE_CODE:
                                if (isset($productData['child_sku'])) {
                                    $childSku = $productData['child_sku'];
                                    if (is_array($productData['child_sku'])) {
                                        $childSku = current($productData['child_sku']);
                                    }
                                    $childProduct = $this->productRepository->get($childSku);
                                    $productTypeInstance = $product->getTypeInstance();
                                    $productTypeInstance->setStoreFilter($product->getStoreId(), $product);

                                    $attributes = $productTypeInstance->getConfigurableAttributes($product);
                                    $superAttributeList = [];
                                    foreach($attributes as $_attribute){
                                        $attributeCode = $_attribute->getProductAttribute()->getAttributeCode();;
                                        $superAttributeList[$_attribute->getAttributeId()] = $childProduct->getData($attributeCode);
                                    }
                                    $params = array(
                                        'form_key' => $this->formKey->getFormKey(),
                                        'product' => $product->getId(),
                                        'qty'   => $qty,
                                        'super_attribute' => $superAttributeList
                                    );
                                    if (isset($productData['price'])) {
                                        $product->setCustomPrice($productData['price']);
                                    }
                                    $this->cart->addProduct($product, $params);
                                }
                                break;
                            case ModelProductBundle::TYPE_CODE:
                                $options = $this->bundleOption->getResourceCollection()
                                    ->setProductIdFilter($product->getId())
                                    ->setPositionOrder()
                                    ->joinValues($store->getId());
                                $params = array(
                                    'form_key' => $this->formKey->getFormKey(),
                                    'product' => $product->getId(),
                                );
                                $selectionCollection = $product->getTypeInstance(true)
                                    ->getSelectionsCollection(
                                        $product->getTypeInstance(true)->getOptionsIds($product),
                                        $product
                                    );

                                if (isset($productData['child_skus']) && is_array($productData['child_skus'])) {
                                    $bundleOptions = [];
                                    $qtyBundleOptions = [];
                                    foreach ($productData['child_skus'] as $childData) {
                                        if (isset($childData['sku'])) {
                                            foreach ($selectionCollection as $selection) {
                                                if ($selection->getOptionId() && $selection->getSku() == $childData['sku']) {
                                                    $bundleOptions[$selection->getOptionId()] = $selection->getSelectionId();
                                                    if (isset($childData['qty'])) {
                                                        $qtyBundleOptions[$selection->getOptionId()] = $childData['qty'];
                                                    } else {
                                                        $qtyBundleOptions[$selection->getOptionId()] = 1;
                                                    }
                                                }
                                            }
                                        }

                                    }
                                    $params['bundle_option'] = $bundleOptions;
                                    $params['bundle_option_qty'] = $qtyBundleOptions;
                                    $this->cart->addProduct($product, $params);
                                }
                                break;
                            case ModelProductGrouped::TYPE_CODE:
                                if (isset($productData['child_skus']) && is_array($productData['child_skus'])) {
                                    $groupedOptions = [];
                                    foreach ($productData['child_skus'] as $childData) {
                                        if (isset($childData['sku'])) {
                                            $childProduct = $this->productRepository->get($childData['sku']);
                                            $groupedOptions[$childProduct->getId()] = 0;
                                            if(isset($childData['qty'])) {
                                                $groupedOptions[$childProduct->getId()] = $childData['qty'];
                                            }
                                        }
                                    }
                                }
                                $params = array(
                                    'form_key' => $this->formKey->getFormKey(),
                                    'product' => $product->getId(),
                                    'item' => $product->getId(),
                                    'super_group' => $groupedOptions,
                                );
                                $this->cart->addProduct($product, $params);
                                break;
                        }


                    } catch (\Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }

                }
            }
            $this->cart->save();
            $usages = $this->getUsages();
            $this->setUsages(++$usages)->save();
            $this->requestToQuote
                ->setRefId($this->getId())
                ->setQuoteId($this->cart->getQuote()->getId())
                ->save();
        }
    }
}
