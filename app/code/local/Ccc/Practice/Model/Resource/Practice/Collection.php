<?php
class Ccc_practice_Model_Resource_Practice_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() 
    {
        $this->_init('practice/practice');
    }
}
