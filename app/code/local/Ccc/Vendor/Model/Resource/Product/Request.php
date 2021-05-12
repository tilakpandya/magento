<?php
class Ccc_Vendor_Model_Resource_Product_Request extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct() {
        $this->_init('vendor/product_request','request_id');
    }
    
}