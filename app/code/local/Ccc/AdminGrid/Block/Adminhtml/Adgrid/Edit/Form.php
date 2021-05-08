<?php
class Ccc_AdminGrid_Block_Adminhtml_Adgrid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
            'legend' => 'AdminGrid Information',
             'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('item', 'text', [
            'name' => 'Admingrid[item]',
            'label' => 'Item',
            'required' => true
        ]);

        if (Mage::registry('AdminGrid_data')) {
            $form->setValues(Mage::registry('AdminGrid_data')->getData());
        }

            return parent::_prepareForm();
        }
    }