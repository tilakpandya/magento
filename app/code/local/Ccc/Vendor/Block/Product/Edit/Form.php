<?php
class Ccc_Vendor_Block_Product_Edit_Form extends Mage_Core_Block_Template
{
    public function __construct() {
        $this->setTemplate('vendor/product/edit/form.phtml');
    }
    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
    public function getSaveUrl()
    {
        if (!$this->getCurrentProduct()) {
            return $this->getUrl('*/*/save');
        }
        $id = $this->getCurrentProduct()['entity_id'];
        return $this->getUrl('*/*/save', ['id' => $id]);
    }

    public function getDeleteUrl()
    {
        $id = $this->getCurrentProduct()['entity_id'];
        return $this->getUrl('*/*/delete', ['entity_id' => $id]);
    }

    public function getCurrentProduct()
    {
        return Mage::registry('current_product');
    }
}


?>