<?php

class Ccc_Vendor_Block_Widget_Name extends Ccc_Vendor_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
        // default template location
        $this->setTemplate('vendor/widget/name.phtml');
    }

    public function showMiddlename()
    {
        return (bool) $this->_getAttribute('middlename')->getIsVisible();
    }

    public function isMiddlenameRequired()
    {
        return (bool) $this->_getAttribute('middlename')->getIsRequired();
    }

    public function getClassName()
    {
        if (!$this->hasData('class_name')) {
            $this->setData('class_name', 'vendor-name');
        }
        return $this->getData('class_name');
    }

    public function getContainerClassName()
    {
        $class = $this->getClassName();
        $class .= $this->showMiddlename() ? '-middlename' : '';
        return $class;
    }

    public function getStoreLabel($attributeCode)
    {
        $attribute = $this->_getAttribute($attributeCode);
        return $attribute ? $this->__($attribute->getStoreLabel()) : '';
    }
}