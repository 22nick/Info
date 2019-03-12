<?php
namespace Tunik\Info\Model\Config\Source;
  
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
use Tunik\Info\Helper\Data;

/**
 * @api
 * @since 
 */
class Brands extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function __construct(Data $helper)
    {
               $this->helper = $helper;
    }
    public function getAllOptions()
    {
        $brandList = $this->helper->getBrandList();

        $this->_options = [ 
            ['label'=>'', 'value'=>''],
            ['label'=>'', 'value'=>'0']
        ];

        foreach ($brandList as $item)
        {
            $this->_options[] = ['label'=>$item->getTitle(), 'value'=>$item->getProductAttr()];
        }
        
        return $this->_options;
    }
  
    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}