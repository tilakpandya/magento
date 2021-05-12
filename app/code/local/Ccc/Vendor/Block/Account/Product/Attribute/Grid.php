<?php
class Ccc_Vendor_Block_Account_Product_Attribute_Grid extends Mage_Core_Block_Template
{
    protected function getAttributes()
    {
        $vendorId = $this->_getSession()->getVendor()->getId();
        $collection = Mage::getResourceModel('vendor/product_attribute_collection')->addFieldToFilter('attribute_code', array('like' => $vendorId . '%'));


        $attributeSetId = Mage::getModel('vendor/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        $collection->getSelect()->joinLeft(
            array('product_attribute'=> 'eav_entity_attribute'),
            "product_attribute.attribute_id = main_table.attribute_id
            AND product_attribute.attribute_set_id = $attributeSetId",
            array('attribute_group_Id'));
        
        return $collection;
    }

    public function getGroupName($groupId)
    {
        $group = Mage::getModel('vendor/product_attribute_group')->load($groupId,'attribute_group_id');
       
        return $group->getGroupName();
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