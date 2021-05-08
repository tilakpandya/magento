<?php
class Ccc_Practice_Adminhtml_practiceController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('practice/practice');
    }
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Practice"));
        $this->_addContent($this->getLayout()->createBlock('practice/adminhtml_practice'));
        $this->renderLayout(); 
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $practice = Mage::getModel('practice/practice')->load($id);
        
        if ($practice->getId()) {    
  
            Mage::register('Practice_data',$practice);
         }
         $this->loadLayout();
         $this->_addContent($this->getLayout()->createBlock('practice/adminhtml_practice_edit'));        
         $this->renderLayout();
    }

    public function saveAction()
    {
        $post = $this->getRequest()->getPost('Practice');
        $id = $this->getRequest()->getParam('id');
        try {
            if ($post) {
                $practice = Mage::getModel('practice/practice');
                if ($id) {
                    $practice = Mage::getModel('practice/practice')->load($id);
                }
                $practice
                ->setName($post['name'])
                ->save();
            }
        } catch (\Throwable $th) {
            Mage::getSingleton('core/session')->addError('Unable to submit your request. Please, try again later');
            $this->_redirect('*/*/');
            return;
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                Mage::getModel('practice/practice')->setId($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
            } catch (\Throwable $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/index');
    }
}
