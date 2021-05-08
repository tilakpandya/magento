<?php
class Ccc_Practice_Block_Adminhtml_Practice extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_blockGroup = 'practice';
        $this->_controller = 'adminhtml_practice';
        $this->_headerText = $this->__('Grid');
        parent::__construct(); 
    }
}
