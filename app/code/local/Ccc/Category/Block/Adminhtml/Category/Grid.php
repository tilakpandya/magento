<?php

class Ccc_Category_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        
        parent::__construct();
         
        $this->setDefaultSort('id');
        $this->setId('category_category_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        return 'category/category_collection';
    }
     
    protected function _prepareCollection()
    {
        //$collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection = Mage::getModel('category/category')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'id'
            )
        );
         
        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name',
            )
        );  
        return parent::_prepareColumns();
    } 
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',['id'=>$row->getId()]);
    }
}
