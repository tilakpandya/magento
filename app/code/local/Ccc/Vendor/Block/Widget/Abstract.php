<?php


class Ccc_Vendor_Block_Widget_Abstract extends Mage_Core_Block_Template
{
    /* public function getConfig($key)
    {
        return $this->helper('vendor/address')->getConfig($key);
    } */

    public function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    public function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    public function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    public function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }

    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('eav/config')->getAttribute('vendor', $attributeCode);
    }
}
