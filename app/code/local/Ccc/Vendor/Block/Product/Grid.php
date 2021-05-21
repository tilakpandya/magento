<?php
class Ccc_Vendor_Block_Product_Grid extends Mage_Core_Block_Template
{
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParams('store',0);
        return Mage::app()->getStore($storeId);
    }

    public function getProducts()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('vendor/product')->getResourceCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            //->addAttributeToSelect('price')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToFilter('vendor_id', ['eq' => $this->getVendor()->getId()]);        
        
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            //$collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'vendor_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'vendor_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            
            $collection->joinAttribute(
                'status',
                'vendor_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            
            $collection->joinAttribute(
                'vendor_id',
                'vendor_product/entity_type_id',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );

            $collection->joinAttribute(
                'price',
                'vendor_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        
            $collection->getSelect()->join(
                array('vendor_product_request' => 'vendor_product_request'),
                'vendor_product_request.product_id = e.entity_id',
                array('vendor_product_request.request_type','vendor_product_request.approve_status')
            );

            
        

        /* $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult(); */
        return $collection;
    }

    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    public function getAddUrl()
    {
        return $this->getUrl('*/*/new');
    }

    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getVendor()
    {
        return $this->_getSession()->getVendor();
    }

    public function getEditUrl()
    {
        return $this->getUrl('*/*/edit');
    }
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete');
    }
    
    
}
