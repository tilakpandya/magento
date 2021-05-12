<?php

class Ccc_Vendor_Product_OrderController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title('Vendor Order');
        $this->renderLayout();
    }
}
