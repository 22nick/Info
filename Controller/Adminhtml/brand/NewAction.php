<?php
namespace Tunik\Info\Controller\Adminhtml\Brand;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Tunik_Info::brand_edit';       
    protected $resultPageFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;        
        parent::__construct($context);
    }
    
    public function execute()
    {
        return $this->resultPageFactory->create();  
    }    
}
