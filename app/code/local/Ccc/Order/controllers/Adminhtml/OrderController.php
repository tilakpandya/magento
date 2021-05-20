<?php
class Ccc_Order_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Sales');
    }

    protected function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        return $this;
    }

    public function indexAction()
    {
        
        $this->_title($this->__('Orders'));
        $this->loadLayout()
            ->_setActiveMenu('order')
            ->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
        //$this->_addContent($this->getLayout()->createBlock('order/adminhtml_order'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function viewAction()
    {
         $this->_title($this->__('Sales'))->_title($this->__('Orders'));
        
        $order = $this->_initOrder();
        if ($order) {

            $isActionsNotPermitted = $order->getActionFlag(
                Mage_Sales_Model_Order::ACTION_FLAG_PRODUCTS_PERMISSION_DENIED
            );
            if ($isActionsNotPermitted) {
                $this->_getSession()->addError($this->__('You don\'t have permissions to manage this order because of one or more products are not permitted for your website.'));
            }

            $this->_initAction();

            $this->_title(sprintf("#%s", $order->getRealOrderId()));

            $this->renderLayout();
        } 
    }

}