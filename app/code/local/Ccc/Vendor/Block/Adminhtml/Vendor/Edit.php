<?php

class Ccc_Vendor_Block_Adminhtml_Vendor_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'vendor';
        $this->_controller = 'adminhtml_vendor';
        parent::__construct();
    }
}
