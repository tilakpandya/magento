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

    protected function _initProduct()
    {
        $this->_title($this->__('Vendor'))
            ->_title($this->__('Manage product vendors'));

        $productId = (int) $this->getRequest()->getParam('id');
        $product   = Mage::getModel('vendor/product')
            ->setStoreId($this->getRequest()->getParam('store', 0))
            ->load($productId);
        
        if (!$productId) {
            if ($setId = (int)$this->getRequest()->getParam('set')) {
                Mage::getModel('adminhtml/session')->setId($setId);
            }
        }    

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
        
   public function newApproveAction()
   {
        $vendor = Mage::getSingleton('vendor/vendor');
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->_initProduct();
        
        try {
            if ($productId && !$product->getId()) {
                $this->_getSession->addError('This product no longer exists');
                $this->_redirect('*/*/');
            }
           
            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')
                ->addFieldtoFilter('product_id',array('eq',$product->getId()))->load()->getLastItem();

            if ($productRequestModel->getRequestType() == 'Edited' && $productRequestModel->getCatalogProductId()) {
                $this->_forward('editApprove');
                return;
            }
            
            if ($productRequestModel->getRequestType() == 'Delete') {
                $this->_forward('delete');
                return;
            }
            
            $catalogProductModel = Mage::getModel('catalog/product');
            $entityType = $catalogProductModel->getResource()->getentityType();
            $defaultAttributesetId = $entityType->getDefaultAttributeSetId();
            $productData = $product->getData();
            unset($productData['entity_id']);
            $catalogProductModel->addData($productData);
            $catalogProductModel->setStoreId($this->getRequest()->getParam('store',0));
            $catalogProductModel->setEntityType($entityType);
            $catalogProductModel->setAttributeSetId($defaultAttributesetId);
            
            
            if ($catalogProductModel->save()) {
               
                $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')
                ->addFieldtoFilter('product_id',array('eq',$product->getId()))->load()->getLastItem();
                
                $productRequestModel->setCatalogProductId($catalogProductModel->getId());
                $productRequestModel->setApproveStatus('Approved');
                $productRequestModel->setCreatedAt($product->getCreatedAt());
                $productRequestModel->setApprovedAt(time());
                $productRequestModel->setRequestType('New');
                  
                $productRequestModel->save();
                /* echo "<pre>";
                print_r($productRequestModel);
                die; */
            }
           
            Mage::getSingleton('core/session')->addSuccess($this->__('New product has been Approved.'));
        } catch (Exception $th) {
            Mage::logException($th);
            Mage::getSingleton('core/session')->addError($th->getMessage());
        }
        $this->_redirect('*/*/');
   } 

   public function editApproveAction()
   {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = Mage::getModel('vendor/product')
            ->setStoreId($this->getRequest()->getParam('store',0))
            ->load($productId);
            
            if (!$productId) {
                if ($setId = (int)$this->getRequest()->getParam('set')) {
                    Mage::getModel('adminhtml/session')->setId($setId);
                }
            }  
            
            try {
                if ($productId && !$product->getId()) {
                    $this->_getSession->addError('This product no longer exists');
                    $this->_redirect('*/*/');
                }
                $productData = $product->getData();
                unset($productData['entityId']);
                unset($productData['entity_Type']);
                unset($productData['attribute_set_id']);
                unset($productData['store_id']);

                $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')
                ->addFieldtoFilter('vendor_id',array('eq',$product->getVendorId()))->load()->getLastItem();

                $catalogProductModel = Mage::getModel('catalog/product');
                $catalogProductId = $catalogProductModel->getCatalogProductId();
                
                if ($catalogProductModel->load($catalogProductId)) {
                    $catalogProductModel->addData($productData);
                    $catalogProductModel->save();
                    $productRequestModel->setRequestType('Edited');
                    $productRequestModel->setApproveStatus('Approved');
                    $productRequestModel->setCreatedAt($product->getUpdatedAt());
                    $productRequestModel->setApproveAt(time());
                    $productRequestModel->save();
                    Mage::getSingleton('core/session')->addSuccess($this->__('Edit product Request has been Approved.'));
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                Mage::logException($th);
                Mage::getSingleton('core/session')->addError($th->getMessage());
                $this->_redirect('*/*/');
            }
   }

   public function rejectAction()
   {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->_initProduct();
        try {
            if ($productId && !$product->getId()) {
                $this->_getSession->addError('This product no longer exists');
                $this->_redirect('*/*/');
            }
            $productRequestModel = Mage::getResourceModel('vendor/product_request_collection')
                ->addFieldtoFilter('product_id',array('eq',$product->getId()))->load()->getLastItem();
             $productRequestModel->setApproveStatus('Rejected');
             $productRequestModel->setCreatedAt($product->getCreatedAt());
             $productRequestModel->setApproveAt(time());
             $productRequestModel->save();
             Mage::getSingleton('core/session')->addSuccess($this->__('product has been Rejected.'));

        } catch (Exception $e) {
            Mage::logException($e);
                Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
   }

   public function deleteAction()
    {
        try {
            $productModel = Mage::getModel('vendor/product');

            if (!($productId = (int) $this->getRequest()->getParam('id')))
                throw new Exception('Id not found');

            if (!$productModel->load($productId)) {
                throw new Exception('product does not exist');
            }

            if (!$productModel->delete()) {
                throw new Exception('Error in delete record', 1);
            }

            Mage::getSingleton('core/session')->addSuccess($this->__('The product has been deleted.'));

        } catch (Exception $e) {
            Mage::logException($e);
            $Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        
        $this->_redirect('*/*/');
    }

}

?>