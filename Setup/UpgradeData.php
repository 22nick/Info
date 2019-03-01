<?php
namespace Tunik\Info\Setup;
/**
 * The MIT License (MIT)
 * Copyright (c) 2015 - 2019 Pulse Storm LLC, Alan Storm
 * 
 * Permission is hereby granted, free of charge, to any person obtaining 
 * a copy of this software and associated documentation files (the 
 * "Software"), to deal in the Software without restriction, including 
 * without limitation the rights to use, copy, modify, merge, publish, 
 * distribute, sublicense, and/or sell copies of the Software, and to 
 * permit persons to whom the Software is furnished to do so, subject to 
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT 
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR 
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;    

//To add an 'brand' dropdown attribtue to products
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory; //To add 'brand' dropdown attribtue to products
    
    protected $scriptHelper;    
    
    public function __construct(
        \Tunik\Info\Setup\Scripts $scriptHelper,
        EavSetupFactory $eavSetupFactory,
        Config $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory; //To add 'brand' dropdown attribtue to products
        $this->eavConfig = $eavConfig;
        
        $this->scriptHelper = $scriptHelper;
    }        

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup, 
        ModuleContextInterface $context
    )
    {
        //To add 'brand' dropdown attribtue to products
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
  
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'brand',
            [
                'group' => 'Brand group',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Brand',
                'input' => 'select',
                'note' => 'Brand',
                'class' => '',
                'source' => 'Tunik\Info\Model\Config\Source\Brands',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'option' => [ 
                    'values' => [],
                ]
            ]    
        );   
        
        $setup->startSetup();        
        $this->scriptHelper->run($setup, $context, 'data');
        $setup->endSetup();
    }        
}
