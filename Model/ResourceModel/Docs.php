<?php
namespace Tunik\Info\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;
use Tunik\Info\Model\DocsUploader;

class Docs extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_docsUploader;
    
    protected function _construct()
    {
        $this->_init('tunik_info_docs','docs_id');
    }

    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $docName = $object->getFileLink();
        $this->_getDocsUploader()->deleteDoc($docName, 'dir');

        return $this;
    }

    private function _getDocsUploader()
    {
        if ($this->_docsUploader === null) {
            $this->_docsUploader = ObjectManager::getInstance()->get(DocsUploader::class);
        }
        return $this->_docsUploader;
    }
}
