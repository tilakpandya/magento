<?php
class Ccc_Category_Model_Resource_Category_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() 
    {
        $this->_init('category/category');
    }
}
