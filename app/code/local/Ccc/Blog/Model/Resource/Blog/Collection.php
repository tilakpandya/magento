<?php
class Ccc_Blog_Model_Resource_Blog_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
 
    protected function _construct()
    {
        $this->_init('blog/blog');
    }
 
}