<?php

class Ccc_Vendor_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('vendorProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('vendor_product_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();

        $collection = Mage::getModel('vendor/product')->getResourceCollection()
            //->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            //->addAttributeToSelect('status')
            //->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('vendor_id'); 

        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $collection->joinAttribute(
            'id',
            'vendor_product/entity_id',
            'entity_id',
            null,
            'inner'
        );

        /* $collection->joinAttribute(
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
            $adminStore
        );
        $collection->joinAttribute(
            'sku',
            'vendor_product/sku',
            'entity_id',
            null,
            'inner',
            $adminStore
        );
        
        $collection->joinAttribute(
            'vendor_id',
            'vendor_product/entity_type_id',
            'entity_id',
            null,
            'inner',
            $adminStore
        );

        $collection->joinAttribute(
            'price',
            'vendor_product/price',
            'entity_id',
            null,
            'left',
            $adminStore
        );

        $collection->joinAttribute(
            'status',
            'vendor_product/status',
            'entity_id',
            null,
            'inner',
            $adminStore
        ); */

        $collection->getSelect()->join(
            array('vendor_product_request' => 'vendor_product_request'),
            'vendor_product_request.product_id = e.entity_id',
            array('vendor_product_request.request_type','vendor_product_request.approve_status')
        )->where('vendor_product_request.approve_status = "Pending"');

        

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id',
            array(
                'header' => Mage::helper('vendor')->__('id'),
                'width'  => '50px',
                'index'  => 'entity_id',
            ));
        $this->addColumn('name',
            array(
                'header' => Mage::helper('vendor')->__('Name'),
                'width'  => '50px',
                'index'  => 'name',
            ));

        $this->addColumn('sku',
            array(
                'header' => Mage::helper('vendor')->__('SKU'),
                'width'  => '50px',
                'index'  => 'sku',
            ));

        $this->addColumn('price',
            array(
                'header' => Mage::helper('vendor')->__('Price'),
                'width'  => '50px',
                'index'  => 'price',
        ));

        $this->addColumn('request_type',
            array(
                'header' => Mage::helper('vendor')->__('Request'),
                'width'  => '50px',
                'index'  => 'request_type',
        ));

        $this->addColumn('approve_status',
            array(
                'header'   => Mage::helper('vendor')->__('Approve Status'),
                'width'    => '50px',
                'index'  => 'approve_status',
                'type' => 'text'
        ));

        $this->addColumn('action1',
            array(
                'header'   => Mage::helper('vendor')->__('Approve Request'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption'=>$this->__('Approve'),
                        'url' => array(
                            'base' => '*/*/newApprove', 
                        ),
                        'field' => 'id',
                    ),
                ),
                'filter'    => false,
                'sortable'  =>false,
        )); 
        
        $this->addColumn('action2',
            array(
                'header'   => Mage::helper('vendor')->__('Reject Request'),
                'width'    => '50px',
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'caption'=>$this->__('Reject'),
                        'url' => array(
                            'base' => '*/*/reject', 
                        ),
                        'field' => 'id',
                    ),
                ),
                'filter'    => false,
                'sortable'  =>false,
        )); 
        parent::_prepareColumns();
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id'    => $row->getId())
        );
    }
}
