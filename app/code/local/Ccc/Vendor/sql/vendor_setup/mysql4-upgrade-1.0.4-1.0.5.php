<?php
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable(array('vendor/product_attribute_group')))
    ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Group ID')
    ->addColumn('attribute_group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Attribute Group ID')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Vendor Id')
    ->addColumn('group_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Group Name')
    ->addForeignKey(
        $installer->getFkName(
            'vendor/product_attribute_group',
            'vendor_id',
            'vendor/vendor',
            'entity_id'
        ),
        'vendor_id', $installer->getTable('vendor/vendor'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'vendor/product_attribute_group',
            'attribute_group_id',
            'eav/attribute_group',
            'attribute_group_id'
        ),
        'attribute_group_id', $installer->getTable('eav/attribute_group'), 'attribute_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('vendor Product Attribute Group Backend Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();