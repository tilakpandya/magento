<?php

class Ccc_Vendor_Block_Account_Dashboard extends Mage_Core_Block_Template
{
    protected $_subscription = null;

    public function getVendor()
    {
        return Mage::getSingleton('vendor/session')->getVendor();
    }

    public function getAccountUrl()
    {
        return Mage::getUrl('vendor/account/edit', array('_secure'=>true));
    }


    public function getOrdersUrl()
    {
        return Mage::getUrl('vendor/order/index', array('_secure'=>true));
    }

    public function getReviewsUrl()
    {
        return Mage::getUrl('review/vendor/index', array('_secure'=>true));
    }

    public function getWishlistUrl()
    {
        return Mage::getUrl('vendor/wishlist/index', array('_secure'=>true));
    }

    public function getTagsUrl()
    {

    }
    
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('vendor/account/');
    }
}
