<?php 
/**
 * This script `included` via class method, inherits this variable from that context
 * @var $setup \Magento\Framework\Setup\SchemaSetupInterface
 */
 $setup;

/**
 * This script `included` via class method, inherits this variable from that context
 * @var $setup \Magento\Framework\Setup\ModuleContextInterface
 */
 $context;
 
//create a table
//         $table = $setup->getConnection()
//             ->newTable($setup->getTable(Gallery::GALLERY_VALUE_TO_ENTITY_TABLE))
//             ->addColumn(
//                 'value_id',
//                 \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
//                 null,
//                 ['unsigned' => true, 'nullable' => false],
//                 'Value media Entry ID'
//             )
//         $setup->getConnection()->createTable($table);

//update a table
// $installer = $setup;
// $tableAdmins = $setup->getTable('admin_user');
// 
// $setup->getConnection()->addColumn(
//     $tableAdmins,
//     'failures_num',
//     [
//         'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
//         'nullable' => true,
//         'default' => 0,
//         'comment' => 'Failure Number'
//     ]
// );
        //"Brand"
        
        $table = $setup->getConnection()->newTable(
            $setup->getTable('tunik_info_brand')
        )->addColumn(
            'brand_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false, ],
            'Demo Title'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
            null,
            [ 'nullable' => false ],
            'Description'
        )->addColumn(
            'product_attr',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => true ],
            'Product attribute'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, ],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE, ],
            'Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [ 'nullable' => false, 'default' => '1', ],
            'Is Active'
        );
        $setup->getConnection()->createTable($table);
