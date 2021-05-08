<?php

class Ccc_Vendor_Block_Adminhtml_Vendor_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Widget_Form
{

    public function getVendor()
    {
        return Mage::registry('current_vendor');
    }

    protected function _prepareLayout()
    {
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        parent::_prepareLayout();
           
    }

    protected function _prepareForm()
    {

        $group = $this->getGroup();

        $attributes = $this->getAttributes();

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $form->setDataObject($this->getVendor());
        $form->setHtmlIdPrefix('group_' . $group->getId());
        $form->setFieldNameSuffix('account');
        $fieldset = $form->addFieldset('fieldset_group_' . $group->getId(), array(
            'legend'    => Mage::helper('vendor')->__($group->getAttributeGroupName()),
            'class'     => 'fieldset',
        ));


        $this->_setFieldset($attributes, $fieldset);

        $form->addValues($this->getVendor()->getData());

        return parent::_prepareForm();
    }


}
