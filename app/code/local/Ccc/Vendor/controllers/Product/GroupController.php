<?php
class Ccc_Vendor_Product_GroupController extends Mage_Core_Controller_Front_Action
{
    protected $_entityTypeId;

    public function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function indexAction()
    {
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/account/login');
        }
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Group'));
        $this->_initLayoutMessages('vendor/session');
        $this->renderLayout();
    }

    public function editAction()
    {
        try {
            $id = (int) $this->getRequest()->getParam('group');
            $session = $this->_getSession();
            $model = Mage::getModel('vendor/product_attribute_group');

            if ($id && !$model->load($id)->getId()) {
                throw new Exception("Invalid group id");
            }

            if ($id) {
                if ($model->getVendorId() != $session->getVendor()->getId()) {
                    $session->addError(
                        Mage::helper('vendor')->__('This group cannot be edited.'));
                    $this->_redirect('*/*/');
                    return;
                }
            }
            Mage::register('attribute_group', $model);
            $this->loadLayout();
            $this->getLayout()->getBlock('head')->setTitle($this->__('Edit Attribute'));
            $this->_initLayoutMessages('vendor/session');
            $this->renderLayout();
        } catch (Exception $e) {
            $session->addError($e->getMessage());
            $this->_redirect('*/*/index');
        }
    }

    public function saveAction()
    {
        echo "<pre>";
        if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('vendor/account/login');
        }
        $data = $this->getRequest()->getPost();
        
        if ($data) {
            $vendor = $this->_getSession()->getVendor();
            $product = Mage::getModel('vendor/product');
            $attributeSetId = $product->getResource()->getEntityType()->getDefaultAttributeSetId();
            $model = Mage::getModel('eav/entity_attribute_group');
            $id = $this->getRequest()->getParam('group_id');
            $groupName = $vendor->getId().'_'.$this->getRequest()->getPost('group_name');
           
            $model->setAttributeGroupName($groupName)
                ->setAttributeSetId($attributeSetId);
            print_r($model); 
            if ($model->itemExists()) {
                Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('A Group With same name exist already.'));
                $this->_redirect('*/*/edit');
            }
                try {
                    $modelGroup = Mage::getModel('vendor/product_attribute_group');
                    if ($attributeSetId = $modelGroup->load($id)->getAttributeGroupId()) {
                        $model->setAttributeGroupId($attributeSetId);
                    }
                    $model->save();
                   
                    $modelGroup->setVendorId($vendor->getId());
                    $modelGroup->setAttributeGroupId($model->getId());
                    $modelGroup->setGroupName($this->getRequest()->getPost('group_name'));
                    $modelGroup->save();
                    Mage::getSingleton('vendor/session')->addSuccess(Mage::helper('vendor')->__('Information added successfully.'));                
                } catch (Exception $e) {
                    Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('An error occuring while save.'));
                }
        }
        print_r($modelGroup);   
        
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        try {
            $modelGroup = Mage::getModel('vendor/product_attribute_group');
           
            if (!($Id = (int) $this->getRequest()->getParam('group_id')))
                throw new Exception('Id not found');

            if (!$modelGroup->load($Id)) {
                throw new Exception('product does not exist');
            }

            if (!$modelGroup->delete()) {
                throw new Exception('Error in delete record');
            } 
            Mage::getSingleton('vendor/session')->addSuccess(Mage::helper('vendor')->__('Group Deleted successfully.'));                

        } catch (Exception $e) {
            echo "<pre>";
            print_r($e);
            die;
            Mage::getSingleton('vendor/session')->addError(Mage::helper('vendor')->__('An error occuring while Delete.'));

        }
        
        $this->_redirect('*/*/');
    }
}
