<?php
class Ccc_Vendor_Block_Product_Edit extends Mage_Core_Block_Template
{
    public function prepareLayout()
    {
        $setModel = Mage::getModel('eav/entity_attribute_set');
        $product = Mage::registry('current_product');
        $productId = $product->getId();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set',null);
        }

        return Mage::getBlockSingleton('vendor/product_edit_form');
    }
}
