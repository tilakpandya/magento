<?php
$installer = $this;

$installer->startSetup();

/**
* create lesson05 table
*/
$installer->run("
ALTER TABLE `vendor_product_request` MODIFY COLUMN `catalog_product_id` INT(10) NULL"
);
$installer->endSetup();