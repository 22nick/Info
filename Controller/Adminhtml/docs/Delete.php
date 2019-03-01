<?php
namespace Tunik\Info\Controller\Adminhtml\Docs;

class Delete extends \Magento\Backend\App\Action
{  
    const ADMIN_RESOURCE = 'Tunik_Info::docs_edit';
    
    /**
     * @var \Tunik\Info\Model\DocsRepository
     */
    protected $objectRepository;

    /**
     * Delete constructor.
     * @param \Tunik\Info\Model\DocsRepository $objectRepository
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Tunik\Info\Model\DocsRepository $objectRepository,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->objectRepository = $objectRepository;

        parent::__construct($context);
    }
          
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('object_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // delete model
                $this->objectRepository->deleteById($id);
                // display success message
                $this->messageManager->addSuccess(__('You have deleted the object.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['docs_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can not find an object to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
        
    }    
    
}
