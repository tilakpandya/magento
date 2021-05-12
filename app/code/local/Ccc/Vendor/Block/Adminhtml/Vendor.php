<?php
class Ccc_Vendor_Block_Adminhtml_Vendor extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_vendor';
        $this->_headerText = $this->__('Vendor Grid');
        parent::__construct();
    }
}
