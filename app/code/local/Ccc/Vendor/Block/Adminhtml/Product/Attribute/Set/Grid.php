<?php

class Ccc_Vendor_Block_Adminhtml_Product_Attribute_Set_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('setGrid');
        $this->setDefaultSort('set_name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::registry('entityType'));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('set_name', array(
            'header'    => Mage::helper('vendor')->__('Set Name'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'attribute_set_name',
        ));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getAttributeSetId()));
    }

}
