<?php

namespace Elisa\ProductApi\Model\ProductDataBuilder;

use Elisa\ProductApi\Api\Data\ElisaProduct\ProductDataInterface;
use Elisa\ProductApi\Api\ParentProductIdProviderInterface;
use Elisa\ProductApi\Api\ProductDataBuilderInterface;
use Elisa\ProductApi\Model\DataBuilderContext as Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Area;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Basic implements ProductDataBuilderInterface
{
    /** @var string|null */
    protected $altNameAttributeCode;
    /** @var Context */
    protected $context;
    /** @var ImageHelper */
    protected $imageHelper;
    /** @var string */
    protected $mainImageId;
    /** @var ParentProductIdProviderInterface */
    protected $parentProductIdProvider;

    /**
     * @param ParentProductIdProviderInterface $parentProductIdProvider
     * @param ImageHelper $imageHelper
     * @param Context $context
     * @param string $mainImageId
     * @param string|null $altNameAttributeCode
     */
    public function __construct(
        ParentProductIdProviderInterface $parentProductIdProvider,
        ImageHelper $imageHelper,
        Context $context,
        string $mainImageId = 'product_page_image_large',
        ?string $altNameAttributeCode = null
    ) {
        $this->altNameAttributeCode = $altNameAttributeCode;
        $this->context = $context;
        $this->imageHelper = $imageHelper;
        $this->mainImageId = $mainImageId;
        $this->parentProductIdProvider = $parentProductIdProvider;
    }

    /**
     * @inheritDoc
     */
    public function execute(
        ProductDataInterface $productData,
        ProductInterface $product,
        ?ProductInterface $parentProduct = null
    ): ?array {
        if (($product instanceof \Magento\Catalog\Model\Product) === false) {
            return null;
        }

        $parentIds = $this->parentProductIdProvider->execute([$product->getId()]);

        if ($parentIds) {
            $productData->setParentIds($parentIds);
        }

        $storeId = $this->context->getStoreManager()->getStore()->getId();
        $this->context->getAppEmulation()->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);

        $name = $this->altNameAttributeCode
            ? ($product->getData($this->altNameAttributeCode) ?: $product->getName())
            : $product->getName();

        $productData->setEntityId($product->getId())
            ->setSku($product->getSku())
            ->setName($name)
            ->setPrice($product->getFinalPrice())
            ->setSuggestedRetailPrice($product->getPrice() ?? 0)
            ->setUrl($product->getProductUrl())
            ->setTypeId($product->getTypeId())
            ->setStatus($product->getStatus())
            ->setVisibility($product->getVisibility() ?? Visibility::VISIBILITY_NOT_VISIBLE)
            ->setWebsiteIds($product->getWebsiteIds());

        $imageUrl = $this->imageHelper->init($product, $this->mainImageId)->getUrl();
        $productData->setMainImage($imageUrl);

        $productImages = $product->getMediaGalleryImages();
        $otherImages = [];

        foreach ($productImages as $productImage) {
            $otherImages[] = $productImage->getUrl();
        }

        $productData->setOtherImages($otherImages);

        $this->context->getAppEmulation()->stopEnvironmentEmulation();

        return null;
    }
}
