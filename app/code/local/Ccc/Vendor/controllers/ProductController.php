<?php
class Ccc_Vendor_ProductController extends Mage_Core_Controller_Front_Action
{
    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = Mage::getModel('vendor/product')->setStoreId($this->getRequest()->getParam('store',0));

        if (!$productId) {
            if ($setId = $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }
            if ($typeId = $this->getRequest()->getParam('set')) {
                $product->setTypeId($typeId);
            }
        }else{
            $product->load($productId);
        }
        Mage::register('current_product',$product);
        return $product;
    }
    public function indexAction()
    {
       $this->loadLayout();
       $this->_initLayoutMessages('vendor/session');
       $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
           $this->_redirect('vendor/account/login');
        }
       $productId = (int) $this->getRequest()->getParam('id');
       $product = $this->_initProduct();

       if ($productId && !$product->getId()) {
           $this->_getSession()->addError($this->__('This product no longer exist'));
           $this->_redirect('*/*/');
           return;
       }
       
       $this->loadLayout();
       $this->_initLayoutMessages('vendor/session');
       $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            if (!$this->getRequest()->getPost()) {
                throw new Exception("Error Processing Request");
                
            }
            $productId = $this->getRequest()->getParam('id');
            $productData = $this->getRequest()->getPost();
            $product = Mage::getSingleton('vendor/product');
            $sku = $this->getRequest()->getPost('sku');
            $skuflag = 1;

            if ($productId && !$product->load($productId)) {
                throw new Exception("Invalid Product Id..");
            }

            if ($sku != $product->getSku()) {
                $skuflag = 1;
            }else {
                $skuflag = 0;
            }

            if ($skuflag == 1) {
                $existProductSku = Mage::getModel('vendor/product')->
                    getResource()->getIdBySku($sku);
                
                if ($existProductSku) {
                    throw new Exception("Sku Product Already exist.");
                }
                $existProductCatalogSku = Mage::getModel('catalog/product')->
                getResource()->getIdBySku($sku);
                
                if ($existProductCatalogSku) {
                    throw new Exception("Product sku Already exist.");
                }
            }

            if (!$productId) {
                $entityTypeId = $product->getResource()->getEntityType()->getEntityTypeId();
                $attributeSetId = $product->getResource()->getEntityType()->getDefaultAttributeSetId();
                $vendorId = $this->getSession()->getVendor()->getId();

                $product->setAttributeSetId($attributeSetId);
                $product->setEntityTypeId($entityTypeId);
                $product->setVendorId($vendorId);
            }
            $product->addData($productData);
            $product->save();
        } catch (Exception $th) {
            Mage::getSingleton('core/session')->addError($this->__('Error in processing'));
            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        
    }
}