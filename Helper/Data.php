<?php

namespace Tunik\Info\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper implements DataInterface
{

    protected $brandFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Tunik\Info\Model\BrandFactory $brandFactory
    ) 
    {
        $this->brandFactory = $brandFactory;
        parent::__construct($context);   
    }

    public function getBrandList()
    {
        $brand = $this->brandFactory->create();
        $collection = $brand->getCollection();
        return $collection;
    }
}