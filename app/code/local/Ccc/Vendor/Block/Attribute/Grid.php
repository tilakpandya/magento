<?php

class Ccc_Vendor_Block_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('vendor/vendor_attribute_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

  
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', array(
            'header'=>Mage::helper('vendor')->__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('vendor')->__('Yes'),
                '0' => Mage::helper('vendor')->__('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumnAfter('is_global', array(
            'header'=>Mage::helper('vendor')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('vendor')->__('Store View'),
                Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('vendor')->__('Website'),
                Ccc_Vendor_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('vendor')->__('Global'),
            ),
            'align' => 'center',
        ), 'is_visible');

        $this->addColumn('is_searchable', array(
            'header'=>Mage::helper('vendor')->__('Searchable'),
            'sortable'=>true,
            'index'=>'is_searchable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('vendor')->__('Yes'),
                '0' => Mage::helper('vendor')->__('No'),
            ),
            'align' => 'center',
        ), 'is_user_defined');

        $this->addColumnAfter('is_filterable', array(
            'header'=>Mage::helper('vendor')->__('Use in Layered Navigation'),
            'sortable'=>true,
            'index'=>'is_filterable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('vendor')->__('Filterable (with results)'),
                '2' => Mage::helper('vendor')->__('Filterable (no results)'),
                '0' => Mage::helper('vendor')->__('No'),
            ),
            'align' => 'center',
        ), 'is_searchable');

        $this->addColumnAfter('is_comparable', array(
            'header'=>Mage::helper('vendor')->__('Comparable'),
            'sortable'=>true,
            'index'=>'is_comparable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('vendor')->__('Yes'),
                '0' => Mage::helper('vendor')->__('No'),
            ),
            'align' => 'center',
        ), 'is_filterable');

        return $this;
    }
}
