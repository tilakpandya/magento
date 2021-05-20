<?php

class Ccc_Order_Adminhtml_Order_CreateController extends Mage_Adminhtml_Controller_Action
{
     /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed($this->_getAclResourse());
    }

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

    /**
     * Loading page block
     */
    public function loadBlockAction()
    {
        $request = $this->getRequest();
        try {
            $this->_initSession()
                ->_processData();
        }
        catch (Mage_Core_Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e){
            $this->_reloadQuote();
            $this->_getSession()->addException($e, $e->getMessage());
        }

        $asJson= $request->getParam('json');
        $block = $request->getParam('block');

        $update = $this->getLayout()->getUpdate();
        if ($asJson) {
            $update->addHandle('adminhtml_sales_order_create_load_block_json');
        } else {
            $update->addHandle('adminhtml_sales_order_create_load_block_plain');
        }

        if ($block) {
            $blocks = explode(',', $block);
            if ($asJson && !in_array('message', $blocks)) {
                $blocks[] = 'message';
            }

            foreach ($blocks as $block) {
                $update->addHandle('adminhtml_sales_order_create_load_block_' . $block);
            }
        }
        $this->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        $result = $this->getLayout()->getBlock('content')->toHtml();
        if ($request->getParam('as_js_varname')) {
            Mage::getSingleton('adminhtml/session')->setUpdateResult($result);
            $this->_redirect('*/*/showUpdateResult');
        } else {
            $this->getResponse()->setBody($result);
        }
    }

    protected function _getAclResourse()
    {
        $action = strtolower($this->getRequest()->getActionName());
        if (in_array($action, array('index', 'save')) && $this->_getSession()->getReordered()) {
            $action = 'reorder';
        }
        switch ($action) {
            case 'index':
            case 'save':
                $aclResource = 'sales/order/actions/create';
                break;
            case 'reorder':
                $aclResource = 'sales/order/actions/reorder';
                break;
            case 'cancel':
                $aclResource = 'sales/order/actions/cancel';
                break;
            default:
                $aclResource = 'sales/order/actions';
                break;
        }
        return $aclResource;
    }
    
}
