<?php

class Ccc_Vendor_Model_Resource_Form_Attribute_Collection extends Mage_Eav_Model_Resource_Form_Attribute_Collection
{
   
    protected $_moduleName = 'vendor';

    protected $_entityTypeCode = 'vendor';

    
    protected function _construct()
    {
        parent::_construct();
        $this->_init('eav/attribute', 'customer/form_attribute');
    }

   
    protected function _getEavWebsiteTable()
    {
        return $this->getTable('customer/eav_attribute_website');
    }
}
