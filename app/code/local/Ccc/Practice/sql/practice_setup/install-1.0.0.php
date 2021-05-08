<?php
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('practice/practice'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,[
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
        ],'Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null,[
        'nullable' => false,
        ],'Names');
$installer->getConnection()->createTable($table);  

$installer->endSetup();

