<?php
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable(array('vendor/product_request')))
    ->addColumn('request_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'nullable' => false,
        'primary' => true,
    ), 'request ID')
    ->addColumn('vendor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'vendor ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'product Id')
    ->addColumn('catalog_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'catalog product Id')
    ->addColumn('request_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'request_type')
    ->addColumn('approve_status', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'approve_status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'created_at')
    ->addColumn('approved_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'approved_at')
    ->addForeignKey(
        $installer->getFkName(
            'vendor/product_request',
            'vendor_id',
            'vendor/vendor',
            'entity_id'
        ),
        'vendor_id', $installer->getTable('vendor/vendor'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'vendor/product_request',
            'product_id',
            'vendor/product',
            'entity_id'
        ),
        'product_id', $installer->getTable('vendor/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
            $installer->getFkName(
                'vendor/product_request',
                'catalog_product_id',
                'catalog/product',
                'entity_id'
            ),
            'product_id', $installer->getTable('catalog/product'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('vendor Product Request Backend Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();