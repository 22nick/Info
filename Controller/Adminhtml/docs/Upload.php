<?php

namespace Tunik\Info\Controller\Adminhtml\docs;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 */
class Upload extends \Magento\Backend\App\Action
{
    /**
     * Docs uploader
     *
     * @var \Tunik\Info\Model\DocsUploader
     */
    public $docsUploader;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Tunik\Info\Model\DocsUploader $docsUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Tunik\Info\Model\DocsUploader $docsUploader
    ) {
        parent::__construct($context);
        $this->docsUploader = $docsUploader;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        $fileId = $this->_request->getParam('param_name', 'doc');
        // var_dump($this->docsUploader->mediaDirectory);
        
        try {
            $result = $this->docsUploader->saveFileToTmpDir($fileId);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
