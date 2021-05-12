<?php 
class Ccc_AdminGrid_Block_Adminhtml_Adgrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        
        $this->_blockGroup = 'admingrid';
        $this->_controller = 'adminhtml_adgrid';
        $this->_headerText = $this->__('Grid');
        parent::__construct();
    }
}
