<?php
namespace Tunik\Info\Controller\Adminhtml\Docs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Tunik\Info\Model\DocsUploader;
use Magento\Framework\App\ObjectManager;

            
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Tunik_Info::docs_edit';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    
    /**
     * @var \Tunik\Info\Model\DocsRepository
     */
    protected $objectRepository;

    protected $_docsUploader;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \Tunik\Info\Model\DocsRepository $objectRepository
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        \Tunik\Info\Model\DocsRepository $objectRepository
    ) {
        $this->dataPersistor    = $dataPersistor;
        $this->objectRepository  = $objectRepository;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Tunik\Info\Model\Docs::STATUS_ENABLED;
            }
            if (empty($data['docs_id'])) {
                $data['docs_id'] = null;
            }

            $model = $this->_objectManager->create('Tunik\Info\Model\Docs');

            $id = $this->getRequest()->getParam('docs_id');
            
            $prevDocName = '';

            if ($id) {
                $model = $this->objectRepository->getById($id);
                $prevDocName = $model->getFileLink();
            }

            $data = $this->_filterDocData($data);
            
            $docName = '';
            if (!empty($data['file_link'])) {
                $docName = $data['file_link'];
            }

            $model->setData($data);
            
            try {
                if($prevDocName && ($docName != $prevDocName))
                {
                    $this->_getDocsUploader()->deleteDoc($prevDocName,'dir');
                } 
                
                if($docName != $prevDocName)
                {
                    $data['file_link'] = $this->_getDocsUploader()->moveFileFromTmp($docName);
                }
                
                $model->setData($data);
                $this->objectRepository->save($model);
                
                $this->_getDocsUploader()->deleteTempFiles();

                $this->messageManager->addSuccess(__('You saved the thing.'));
                $this->dataPersistor->clear('tunik_info_docs');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['docs_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('tunik_info_docs', $data);
            return $resultRedirect->setPath('*/*/edit', ['docs_id' => $this->getRequest()->getParam('docs_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    public function _filterDocData(array $rawData)
    {
        //Replace doc with fileuploader field name
        $data = $rawData;
        if (isset($data['file_link'][0]['name'])) {
            $data['file_link'] = $data['file_link'][0]['name'];
        } else {
            $data['file_link'] = null;
        }
        return $data;
    }
    
    private function _getDocsUploader()
    {
        if ($this->_docsUploader === null) {
            $this->_docsUploader = ObjectManager::getInstance()->get(DocsUploader::class);
        }
        return $this->_docsUploader;
    }    
}
