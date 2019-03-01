<?php
namespace Tunik\Info\Model;
class Brand extends \Magento\Framework\Model\AbstractModel implements \Tunik\Info\Api\Data\BrandInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'tunik_info_brand';

    protected function _construct()
    {
        $this->_init('Tunik\Info\Model\ResourceModel\Brand');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
