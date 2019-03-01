<?php
namespace Tunik\Info\Controller\Adminhtml\Docs;

use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
            
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

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \Tunik\Info\Model\DocsRepository $objectRepository
     */
    public function __construct(
        Action\Context $context,
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Tunik\Info\Model\Docs::STATUS_ENABLED;
            }
            if (empty($data['docs_id'])) {
                $data['docs_id'] = null;
            }

            /** @var \Tunik\Info\Model\Docs $model */
            $model = $this->_objectManager->create('Tunik\Info\Model\Docs');

            $id = $this->getRequest()->getParam('docs_id');
            if ($id) {
                $model = $this->objectRepository->getById($id);
            }

            $data = $this->_filterDocData($data);

            $model->setData($data);

            try {
                $this->objectRepository->save($model);
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
}
