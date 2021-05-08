<?php
class Ccc_Blog_Model_Resource_Blog extends Mage_Eav_Model_Entity_Abstract
{
   
    const ENTITY = 'Ccc_blog_blog';
	public function _construct() {
		$this->setType(self::ENTITY)
				->setConnection('core_read','core_write');
		parent::_construct();
	} 

    /* public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('Ccc_blog_blog');
        $this->setConnection(
            $resource->getConnection('blog_read'),
            $resource->getConnection('blog_write')
        );
    } */

    protected function _getDefaultAttributes()
    {
        return [
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        ];
    }
}
