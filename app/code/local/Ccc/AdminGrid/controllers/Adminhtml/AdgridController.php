<?php

class Ccc_AdminGrid_Adminhtml_AdgridController extends Mage_Adminhtml_Controller_Action
{
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admingrid/adgrid');
    }


	public function indexAction()
    {
       	$this->loadLayout();
	    $this->_title($this->__("Admin Grid"));
        $this->_addContent($this->getLayout()->createBlock('admingrid/adminhtml_adgrid'));
	    $this->renderLayout();
    }

    public function saveAction()
    {
        echo "<pre>";
        $post = $this->getRequest()->getPost();
        $id = $this->getRequest()->getParam('id');
        if ($post) {
            try {
                $admingridModel = Mage::getModel('admingrid/adgrid');
                if ($id) {
                    $admingridModel = Mage::getModel('admingrid/adgrid')->load($id);
                }
                $admingridModel->item = $post['Admingrid']['item'];
                $admingridModel->save();
                
            } catch (\Throwable $th) {
                Mage::getSingleton('core/session')->addError('Unable to submit your request. Please, try again later');
                $this->_redirect('*/*/');
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function newAction()
    {
       $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $admingridModel = Mage::getModel('admingrid/adgrid')->load($id);
        
        if ($admingridModel->getId()) {    
  
           Mage::register('AdminGrid_data',$admingridModel);
        }
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('admingrid/adminhtml_adgrid_edit'));        
        $this->renderLayout();
        
    }

    public function deleteAction()
    {
        if($this->getRequest()->getParam('id') > 0 ) {
            try {
                $admingridModel = Mage::getModel('admingrid/adgrid');
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


?>