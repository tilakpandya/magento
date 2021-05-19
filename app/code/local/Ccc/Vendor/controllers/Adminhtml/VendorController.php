<?php
class Ccc_Vendor_Adminhtml_VendorController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('vendor/vendor');
    }

    protected function _initVendor()
    {
        $this->_title($this->__('Vendor'))
            ->_title($this->__('Manage vendors'));

        $vendorId = (int) $this->getRequest()->getParam('id');
        $vendor   = Mage::getModel('vendor/vendor')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($vendorId);

        Mage::register('current_vendor', $vendor);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $vendor;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('vendor');
        $this->_title('Vendor Grid');
        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendor'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $vendorId = (int) $this->getRequest()->getParam('id');
        $vendor   = $this->_initVendor();

        if ($vendorId && !$vendor->getId()) {
            $this->_getSession()->addError(Mage::helper('vendor')->__('This vendor no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->_title($vendor->getName());

        $this->loadLayout();
        
        $this->_setActiveMenu('vendor/vendor');
        //$this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_vendor_edit'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->renderLayout();
    }

    public function saveAction()
    {

        try {

            $vendorData = $this->getRequest()->getPost('account');
            $email = $vendorData['email'];
            $vendorData['password_hash'] = md5($vendorData['password_hash']);
            
            $vendor = Mage::getSingleton('vendor/vendor');

           
            if ($vendorId = $this->getRequest()->getParam('id')) {

                if (!$vendor->load($vendorId)) {
                    throw new Exception("No Row Found");
                }
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            }
            
             if ($vendor->loadByEmail($email)->getEntityId()) {
                throw new Exception("Email Already Exist");
             }
             
            
            $vendor->addData($vendorData)
            ->save(); 
            /* print_r($vendor);
            die; */

            Mage::getSingleton('core/session')->addSuccess("Vendor data added.");
            $this->_redirect('*/*/');

        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }

    }

    public function deleteAction()
    {
        try {

            $vendorModel = Mage::getModel('vendor/vendor');

            if (!($vendorId = (int) $this->getRequest()->getParam('id')))
                throw new Exception('Id not found');

            if (!$vendorModel->load($vendorId)) {
                throw new Exception('vendor does not exist');
            }

            if (!$vendorModel->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The vendor has been deleted.'));

        } catch (Exception $e) {
            Mage::logException($e);
            $Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }
}

?>