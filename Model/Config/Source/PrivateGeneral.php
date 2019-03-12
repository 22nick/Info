<?php
namespace Tunik\Info\Model\Config\Source;

/**
 * @api
 * @since 
 */
class PrivateGeneral implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [];
        $arr[] = ['label' => 'Private', 'value' => '1'];
        $arr[] = ['label' => 'General', 'value' => '2'];
        return $arr;
    }

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
