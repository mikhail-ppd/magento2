<?php
namespace Elisa\ProductApi\Model;

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

class RequestToQuote extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'elisa_productsapi_cartrequest';

    protected $_cacheTag = 'elisa_productsapi_cartrequest_to_quote';

    protected $_eventPrefix = 'elisa_productsapi_cartrequest_to_quote';


    protected function _construct()
    {
        $this->_init('Elisa\ProductApi\Model\ResourceModel\RequestToQuote');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
