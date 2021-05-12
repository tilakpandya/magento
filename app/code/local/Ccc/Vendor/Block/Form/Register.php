<?php

class Ccc_Vendor_Block_Form_Register extends Mage_Directory_Block_Data
{
    
    protected $_address;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('vendor')->__('Create New Customer Account'));
        return parent::_prepareLayout();
    }

   
    public function getPostActionUrl()
    {
        return $this->helper('vendor')->getRegisterPostUrl();
    }

    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = $this->helper('vendor')->getLoginUrl();
        }
        return $url;
    }

    
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = Mage::getSingleton('vendor/session')->getCustomerFormData(true);
            $data = new Varien_Object();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int) $data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }


    public function restoreSessionData(Ccc_Vendor_Model_Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }
}