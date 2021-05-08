<?php

class Ccc_Vendor_Block_Form_Login extends Mage_Core_Block_Template
{
    private $_username = -1;

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('vendor')->__('vendor Login'));
        return parent::_prepareLayout();
    }

    
    public function getPostActionUrl()
    {
        return $this->helper('vendor')->getLoginPostUrl();
    }

    
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if (is_null($url)) {
            $url = $this->helper('vendor')->getRegisterUrl();
        }
        return $url;
    }

 
    public function getForgotPasswordUrl()
    {
        return $this->helper('vendor')->getForgotPasswordUrl();
    }

    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = Mage::getSingleton('vendor/session')->getUsername(true);
        }
        return $this->_username;
    }
}
