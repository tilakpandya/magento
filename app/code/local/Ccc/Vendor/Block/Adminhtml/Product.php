<?php
class Ccc_Vendor_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_product';
        $this->_headerText = $this->__('Vendor Product Grid');
        parent::__construct();
        $this->_removeButton('add');
    }
}
