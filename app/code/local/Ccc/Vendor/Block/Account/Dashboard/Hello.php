<?php

class Ccc_Vendor_Block_Account_Dashboard_Hello extends Mage_Core_Block_Template
{

    public function getVendorName()
    {
        $vendor = Mage::getSingleton('vendor/session')->getVendor();
        $fname = $vendor->getFirstname();
        $lname = $vendor->getLastname();
        return $fname.' '.$lname;
    }

}
