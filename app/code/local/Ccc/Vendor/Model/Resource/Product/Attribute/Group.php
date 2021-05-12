<?php
class Ccc_Vendor_Model_Resource_Product_Attribute_Group extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct() {
        $this->_init('vendor/product_attribute_group','group_id');
    }
}
