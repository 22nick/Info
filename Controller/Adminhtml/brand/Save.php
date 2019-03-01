<?php
namespace Tunik\Info\Controller\Adminhtml\Brand;

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
    const ADMIN_RESOURCE = 'Tunik_Info::brand_edit';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    
    /**
     * @var \Tunik\Info\Model\BrandRepository
     */
    protected $objectRepository;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \Tunik\Info\Model\BrandRepository $objectRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \Tunik\Info\Model\BrandRepository $objectRepository
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
                $data['is_active'] = Tunik\Info\Model\Brand::STATUS_ENABLED;
            }
            if (empty($data['brand_id'])) {
                $data['brand_id'] = null;
            }

            /** @var \Tunik\Info\Model\Brand $model */
            $model = $this->_objectManager->create('Tunik\Info\Model\Brand');

            $id = $this->getRequest()->getParam('brand_id');
            if ($id) {
                $model = $this->objectRepository->getById($id);
            }

            $model->setData($data);

            try {
                $this->objectRepository->save($model);
                $this->messageManager->addSuccess(__('You saved the thing.'));
                $this->dataPersistor->clear('tunik_info_brand');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            $this->dataPersistor->set('tunik_info_brand', $data);
            return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }    
}
