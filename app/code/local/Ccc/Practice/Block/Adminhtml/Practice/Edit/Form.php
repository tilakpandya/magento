<?php
class Ccc_Practice_Block_Adminhtml_Practice_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function _prepareForm()
    {
        $form = new Varien_Data_Form(
            [
                'id' => 'edit',
                'action' => $this->getUrl('*/*/save',['id' => $this->getRequest()->getParam('id')]),
                'method' => 'post',
            ]
        );
        
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', [
            'legend' => 'practice Information',
             'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'Practice[name]',
            'label' => 'name',
            'required' => true
        ]);

        if (Mage::registry('Practice_data')) {
            $form->setValues(Mage::registry('Practice_data')->getData());
        }

            return parent::_prepareForm();
    }
    
}
