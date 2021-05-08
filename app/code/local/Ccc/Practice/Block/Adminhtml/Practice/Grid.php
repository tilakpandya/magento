<?php
class Ccc_Practice_Block_Adminhtml_Practice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setDefaultSort('id');
        $this->setId('practice_practice_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('practice/practice')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    public function _prepareColumns()
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
