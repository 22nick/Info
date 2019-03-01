<?php
namespace Tunik\Info\Model\ResourceModel;
class Docs extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('tunik_info_docs','docs_id');
    }
}
