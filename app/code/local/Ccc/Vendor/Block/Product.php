<?php
class Ccc_Vendor_Block_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'vendor';
        $this->_controller = 'account';
        $this->_headerText = $this->__('Product Grid');
        parent::__construct();
    }
}
