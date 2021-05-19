<?php

class Ccc_Vendor_Block_Product_Edit_Tabs_Attributes extends Mage_Core_Block_Template
{
    protected $_groups;
    protected $_attributes = [];
    protected $visiblity = [
        '1'=>'Not Visible Individually',
        '2'=>'Catalog',
        '3'=>'Search',
        '4'=>'Catalog, Search'
    ];

    protected $status = [
        '0'=>'Disable',
        '1'=>'Enabled'
    ];

    public function __construct() 
    {
        $this->setTemplate('vendor/product/edit/tabs/attributes.phtml');
    }

    public function getCurrentProduct()
    {
        return Mage::registry('current_product');
    }

    public function getVisiblityOption()
    {
        return $this->visiblity;
    }

    public function getStatusOption()
    {
        return $this->status;
    }

    public function getAttributeOptions($attributeId)
    {
        $attributeOptionModel = Mage::getResourceModel('eav/entity_attribute_option_collection');
        $attributeOptionModel->getSelect()->where("main_table.attribute_id = {$attributeId}")
        ->join(array('attribute_option_value'=>'eav_attribute_option_value'),
            'attribute_option_value.option_id = main_table.option_id',
            array('*'));
        $attributeOptionModel = $attributeOptionModel->load();
        return $attributeOptionModel->getItems();    
    }

}
?>