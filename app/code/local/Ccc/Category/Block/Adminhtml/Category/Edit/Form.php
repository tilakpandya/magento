<?php
class Ccc_Category_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            [
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save',['id' => $this->getRequest()->getParam('id')]),
                'method' => 'post',
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', [
            'legend' => 'Category Information',
             'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'Category[name]',
            'label' => 'name',
            'required' => true
        ]);

        if (Mage::registry('Category_data')) {
            $form->setValues(Mage::registry('Category_data')->getData());
        }

            return parent::_prepareForm();
        }
    }