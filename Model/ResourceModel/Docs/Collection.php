<?php
namespace Tunik\Info\Model\ResourceModel\Docs;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Tunik\Info\Model\Docs','Tunik\Info\Model\ResourceModel\Docs');
    }
}
