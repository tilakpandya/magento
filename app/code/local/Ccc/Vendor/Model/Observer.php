<?php
class Ccc_Vendor_Model_Observer
{
    public function beforeLoadLayout($observer)
    {
        $loggedIn = Mage::getSingleton('vendor/session')->isLoggedIn();

        $observer->getEvent()->getLayout()->getUpdate()
            ->addHandle('vendor_logged_' . ($loggedIn ? 'in' : 'out'));
    }

    public function deleteVendorFlowPassword()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('write');
        $condition = array('requested_date < ?' => Mage::getModel('core/date')->date(null, '-1 day'));
        $connection->delete($connection->getTableName('vendor_flowpassword'), $condition);
    }
}
