<?php

class Ccc_Order_Adminhtml_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
    protected function _initSession()
    {
        /**
         * Identify customer
         */
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int) $customerId);
        }

        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int) $storeId);
        }

        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string) $currencyId);
            $this->_getOrderCreateModel()->setRecollect(true);
        }

        //Notify other modules about the session quote
        Mage::dispatchEvent('create_order_session_quote_initialized',
                array('session_quote' => $this->_getSession()));

        return $this;
    }

    public function indexAction()
    {
        
        $this->_title($this->__('Orders'));
        $this->_initSession();
        $this->loadLayout();

        $this->_setActiveMenu('order')
            ->renderLayout();
    }

    public function startAction()
    {
        $this->_getSession()->clear();
        $this->_redirect('*/*', array('customer_id' => $this->getRequest()->getParam('customer_id')));
    }
}
