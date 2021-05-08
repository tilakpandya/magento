<?php
class Ccc_Vendor_Adminhtml_ProductController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('vendor/product');
    }

    protected function _initVendor()
    {
        $this->_title($this->__('Vendor'))
            ->_title($this->__('Manage product vendors'));

        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('product/product')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($productId);

        Mage::register('current_product', $product);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $product;
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('vendor/product');
        $this->_title('Vendor Grid');
        $this->_addContent($this->getLayout()->createBlock('vendor/adminhtml_product'));
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

            
            $vendor = Mage::getSingleton('vendor/vendor');

           
            if ($vendorId = $this->getRequest()->getParam('id')) {

                if (!$vendor->load($vendorId)) {
                    throw new Exception("No Row Found");
                }
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            }
           /*  echo"<pre>";
            print_r($vendorData); */
            
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

}

?>