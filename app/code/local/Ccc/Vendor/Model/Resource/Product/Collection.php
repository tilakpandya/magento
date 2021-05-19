<?php
class Ccc_Vendor_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
	public function __construct()
	{
		$this->setEntity('vendor_product');
		parent::__construct();
		
	}
}