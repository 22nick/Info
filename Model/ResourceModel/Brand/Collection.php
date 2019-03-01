<?php
namespace Tunik\Info\Model\ResourceModel\Brand;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Tunik\Info\Model\Brand','Tunik\Info\Model\ResourceModel\Brand');
    }
}
