<?php
namespace Tunik\Info\Model;
class Docs extends \Magento\Framework\Model\AbstractModel implements \Tunik\Info\Api\Data\DocsInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'tunik_info_docs';

    protected function _construct()
    {
        $this->_init('Tunik\Info\Model\ResourceModel\Docs');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
