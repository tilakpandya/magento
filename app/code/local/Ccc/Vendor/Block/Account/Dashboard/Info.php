<?php

class Ccc_Vendor_Block_Account_Dashboard_Info extends Mage_Core_Block_Template
{
    public function getVendor()
    {
        return Mage::getSingleton('vendor/session')->getVendor();
    }

    public function getChangePasswordUrl()
    {
        return Mage::getUrl('*/account/edit/changepass/1');
    }

    public function getVendorName()
    {
        $vendor = Mage::getSingleton('vendor/session')->getVendor();
        $fname = $vendor->getFirstname();
        $lname = $vendor->getLastname();
        return $fname.' '.$lname;
    }
}
