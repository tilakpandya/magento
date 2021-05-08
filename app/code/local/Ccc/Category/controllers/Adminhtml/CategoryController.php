<?php

class Ccc_Category_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admingrid/adgrid');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("category"));
        $this->_addContent($this->getLayout()->createBlock('category/adminhtml_category'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $category = Mage::getModel('category/category')->load($id);
        
        if ($category->getId()) {    
  
           Mage::register('Category_data',$category);
        }
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('category/adminhtml_category_edit'));        
        $this->renderLayout();
    }

    public function saveAction()
    {
        $post = $this->getRequest()->getPost('Category');
        $id = $this->getRequest()->getParam('id');
        if ($post) {
            try {
                $admingridModel = Mage::getModel('category/category');
                if ($id) {
                    $admingridModel = Mage::getModel('category/category')->load($id);
                }
                $admingridModel->setName($post['name']);
                $admingridModel->save();
                
            } catch (\Throwable $th) {
                Mage::getSingleton('core/session')->addError('Unable to submit your request. Please, try again later');
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        if($this->getRequest()->getParam('id') > 0 ) {
            try {
                $admingridModel = Mage::getModel('category/category');
                $admingridModel->setId($this->getRequest()->getParam('id'))
                ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/index');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
            $this->_redirect('*/*/index');
    }
}