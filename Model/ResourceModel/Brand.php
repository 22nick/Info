<?php
namespace Tunik\Info\Model\ResourceModel;
class Brand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('tunik_info_brand','brand_id');
    }
}
