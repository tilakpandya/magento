<?php
class Ccc_Vendor_Model_Form extends Mage_Eav_Model_Form
{
    protected $_moduleName = 'vendor';
    protected $_entityTypeCode = 'vendor';

    protected function _getFormAttributeCollection()
    {
        return parent::_getFormAttributeCollection()
            ->addFieldToFilter('attribute_code', array('neq' => 'created_at'));
    }
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            $collection = $this->_getFormAttributeCollection();

            $collection->setStore($this->getStore())
                ->setEntityType($this->getEntityType())
                ->addFormCodeFilter($this->getFormCode());

            $this->_attributes = array();
            $this->_userAttributes = array();
            foreach ($collection as $attribute) {

                $this->_attributes[$attribute->getAttributeCode()] = $attribute;
                if ($attribute->getIsUserDefined()) {
                    $this->_userAttributes[$attribute->getAttributeCode()] = $attribute;
                } else {
                    $this->_systemAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }
        return $this->_attributes;
    }
}
