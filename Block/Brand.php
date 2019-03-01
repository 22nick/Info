<?php
namespace Tunik\Info\Block;

// use Magento\Catalog\Block\Product\View;

class Brand extends \Magento\Framework\View\Element\Template
{
    protected $brandFactory;
    protected $_registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Tunik\Info\Model\BrandFactory $brandFactory,
        \Magento\Framework\Registry $registry,        
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->brandFactory = $brandFactory;
        parent::__construct($context, $data);
    }

    function _prepareLayout()
    {
        $brand = $this->brandFactory->create();
        // $product = $this->getPtoduct();
        // var_dump($product);
    }

    public function getBrandInfo()
    {
        $brand = $this->brandFactory->create();
        if ($brandId = $this->getRequest()->getParam('brand_id')) 
        {
            return $brand->load($brandId);
        }
        return false;
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }
    
    public function getInfoByBrandId($brandId)
    {
        $brand = $this->brandFactory->create();
        return $brand->load($brandId);
    }

    public function getCurrentProductInfo()
    {
        $currentProduct = $this->_registry->registry('current_product');
        $brandId = $currentProduct->getBrand();
        $data = getInfoByBrandId($brandId);
        return $data;
    }    
}
