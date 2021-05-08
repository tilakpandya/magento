<?php
class Ccc_Category_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'category';
        $this->_controller = 'adminhtml_category';
        $this->_headerText = $this->__('Form');
        parent::__construct();
    }
}