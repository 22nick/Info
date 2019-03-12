<?php
namespace Tunik\Info\Block;
class Main extends \Magento\Framework\View\Element\Template
{
    protected $docsFactory;
    protected $_registry;
    private $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Tunik\Info\Model\DocsFactory $docsFactory,
        \Magento\Framework\Registry $registry,
        \Tunik\Info\Helper\Data $helper,        
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->docsFactory = $docsFactory;
        $this->helper = $helper;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    function _prepareLayout()
    {
        // $docs = $this->docsFactory->create();
        // $collection = $docs->getCollection();
        
        // $collection->setPageSize(4);

        // $collection->addFieldToFilter('product_id', array('eq'=>'13'));
        
        // echo $collection->getSelect();
        // var_dump($collection);
        
        // $collection = $this->helper->getBrandList();

        // foreach($collection as $item)
        // {
        //     var_dump('Brand: ' . $item->getTitle() . ' <br>');
        // //     if ($item->getProductId()==13)
        // //     {
        // //         var_dump($item->getData());
        // //     }
        // }
        // exit;
    }

    public function getDocs()
    {
        $docs = $this->docsFactory->create();
        if ($docsId = $this->getRequest()->getParam('docs_id')) 
        {
            return $docs->load($docsId);
        }
        return false;
    }
    
    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }

    public function getInfoById($productId)
    {
        $docs = $this->docsFactory->create();
        $collection = $docs->getCollection();

        $collection->addFieldToFilter('product_id', array('eq'=>$productId));        
        
        // var_dump($productId);
        
        // exit;
        
        return $collection; //->getFirstItem();
    }

    public function getInfoByBrand($brandId)
    {
        $docs = $this->docsFactory->create();
        $collection = $docs->getCollection();

        $collection->addFieldToFilter('general_private', array('eq'=>2))
                    ->addFieldToFilter('product_id', array('eq'=>$brandId));        
        return $collection;
    }
    
    public function getDocUrl($doc)
    {
        $mediaUrl = $this->storeManager
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $docUrl = $mediaUrl.'info/tmp/doc/'.$doc;
        return $docUrl;
    }    
}
