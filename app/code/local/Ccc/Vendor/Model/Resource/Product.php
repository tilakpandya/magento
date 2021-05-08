<?php
class Ccc_Vendor_Model_Resource_Product extends Mage_Eav_Model_Entity_Abstract
{

    const ENTITY = 'vendor_product';

    public function __construct()
    {

        $this->setType(self::ENTITY)
            ->setConnection('core_read', 'core_write');

        parent::__construct();
    }
    public function getDefaultAttributeSourceModel()
    {
        return Mage_Eav_Model_Entity::DEFAULT_SOURCE_MODEL;
    }
    public function getIdBySku($sku)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('sku = :sku');

        $bind = array(':sku' => (string)$sku);

        return $adapter->fetchOne($select, $bind);
    }
}
