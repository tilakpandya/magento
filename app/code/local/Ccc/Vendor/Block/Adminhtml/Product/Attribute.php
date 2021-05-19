<?php

class Ccc_Vendor_Block_Adminhtml_Product_Attribute extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
    	$this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_product_attribute';
        $this->_headerText = Mage::helper('vendor')->__('Manage Product Attributes');
        $this->_addButtonLabel = Mage::helper('vendor')->__('Add New Product Attribute');
        parent::__construct();
    }

}
