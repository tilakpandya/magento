<?php
class Ccc_AdminGrid_Block_Adminhtml_Adgrid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'admingrid';
        $this->_controller = 'adminhtml_adgrid';
        $this->_headerText = $this->__('Form');
        parent::__construct();
    }
}