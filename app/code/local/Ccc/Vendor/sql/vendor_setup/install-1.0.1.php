<?php

$this->startSetup();

$this->addEntityType(Ccc_Vendor_Model_Resource_Vendor::ENTITY, [
    'entity_model'                => 'vendor/vendor',
    'attribute_model'             => 'vendor/attribute',
    'table'                       => 'vendor/vendor',
    'increment_per_store'         => '0',
    'additional_attribute_table'  => 'vendor/eav_attribute',
    'entity_attribute_collection' => 'vendor/vendor_attribute_collection',
]);

$this->createEntityTables('vendor');
$this->installEntities();

$default_attribute_set_id = Mage::getModel('eav/entity_setup', 'core_setup')
    						->getAttributeSetId('vendor', 'Default');

$this->run("UPDATE `eav_entity_type` SET `default_attribute_set_id` = {$default_attribute_set_id} WHERE `entity_type_code` = 'vendor'");

$this->endSetup();
