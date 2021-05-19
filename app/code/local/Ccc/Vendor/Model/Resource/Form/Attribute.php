<?php

class Ccc_Vendor_Model_Resource_Form_Attribute extends Mage_Eav_Model_Resource_Form_Attribute
{

    protected function _construct()
    {
        $this->_init('customer/form_attribute', 'attribute_id');
    }
}
