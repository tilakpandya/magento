<?php

class Ccc_Attribute_Block_Adminhtml_Product_Attribute_Set_Main_Formgroup extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('set_fieldset', array('legend' => Mage::helper('catalog')->__('Add New Group')));

        $fieldset->addField('attribute_group_name', 'text',
            array(
                'label'    => Mage::helper('catalog')->__('Name'),
                'name'     => 'attribute_group_name',
                'required' => true,
            )
        );

        $fieldset->addField('submit', 'note',
            array(
                'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'   => Mage::helper('catalog')->__('Add Group'),
                        'onclick' => 'this.form.submit();',
                        'class'   => 'add',
                    ))
                    ->toHtml(),
            )
        );

        $fieldset->addField('attribute_set_id', 'hidden',
            array(
                'name'  => 'attribute_set_id',
                'value' => $this->_getSetId(),
            )

        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $form->setAction($this->getUrl('*/product_group/save'));
        $this->setForm($form);
    }

    protected function _getSetId()
    {
        return (intval($this->getRequest()->getParam('id')) > 0)
        ? intval($this->getRequest()->getParam('id'))
        : Mage::getModel('eav/entity_type')
            ->load(Mage::registry('entityType'))
            ->getDefaultAttributeSetId();
    }
}
