<?php
class Ccc_Vendor_Block_Product_Order_Grid extends Mage_Core_Block_Template
{
    public function getVendor()
    {
        return Mage::getSingleton('vendor/session')->getVendor();
    }
    public function getOrdersList()
    {
        $vendorId = $this->getVendor()->getId();
        $catalogProduct = Mage::getModel('catalog/product')->getResourceCollection();
        /* $catalogProduct->addFieldToFilter('vendor_id',array('eq'=>$vendorId));*/
        
        $catalogProduct->getSelect()->join(
            array('order_item'=>'sales_flat_order_item'),
            'order_item.product_id = e.entity_id',
            array('order_item.order_id','order_item.name','order_item.price') 
    );

        return $catalogProduct->getData(); 
    }
}
