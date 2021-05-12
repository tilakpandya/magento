<?php


class Ccc_Vendor_Block_Adminhtml_Vendor_Attribute_Set_Toolbar_Main extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('vendor/attribute/set/toolbar/main.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('addButton',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('vendor')->__('Add New Set'),
                    'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/add') . '\')',
                    'class' => 'add',
                ))
        );
        return parent::_prepareLayout();
    }

    protected function getNewButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    protected function _getHeader()
    {
        return Mage::helper('vendor')->__('Manage Vendor Attribute Sets');
    }

    protected function _toHtml()
    {
        
        return parent::_toHtml();
    }
}
