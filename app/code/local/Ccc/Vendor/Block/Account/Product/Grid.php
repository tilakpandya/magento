<?php
class Ccc_Vendor_Block_Account_Product_Grid extends Mage_Core_Block_Template
{
    protected function getAttributeGroup()
    {
        $vendorId = $this->_getSession()->getVendor()->getId();
        $collection = Mage::getResourceModel('vendor/product_attribute_group_collection')
        ->addFieldToFilter('vendor_id', array('eq' => $vendorId));
        
        return $collection;
    }

    public function getAddUrl()
    {
        return $this->getUrl('*/*/new');
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getVendor()
    {
        return $this->_getSession()->getVendor();
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit');
    }
}

?>