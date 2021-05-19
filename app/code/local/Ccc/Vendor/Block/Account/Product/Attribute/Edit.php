<?php

class Ccc_Vendor_Block_Account_Product_Attribute_Edit extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
{
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }
    public function getSaveUrl()
    {
        if (!$this->getAttribute()) {
            return $this->getUrl('*/*/save');
        }
        $id = $this->getAttribute()['attribute_id'];
        return $this->getUrl('*/*/save', ['attribute_id' => $id]);
    }

    public function getDeleteUrl()
    {
        $id = $this->getAttribute()['attribute_id'];
        return $this->getUrl('*/*/delete', ['attribute_id' => $id]);
    }

    public function getAttribute()
    {
        return Mage::registry('entity_attribute');
    }

    public function getAttributeObject()
    {
        return Mage::registry('entity_attribute');
    }
    protected function _getSession()
    {
        return Mage::getSingleton('vendor/session');
    }
     public function getAttributeGroups()
    {
        $vendor = $this->_getSession()->getVendor();
        return $collection = Mage::getModel('vendor/product_attribute_group')
            ->getCollection()
            ->addFieldToFilter('vendor_id', array("eq" => $vendor->getId()));
         
    } 
    
    public function getStoreOptionValues($storeId)
    {
        $values = $this->getData('store_option_values_' . $storeId);
        if (is_null($values)) {
            $values = array();
            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setStoreFilter($storeId, false)
                ->load();
            foreach ($valuesCollection as $item) {
                $values[$item->getId()] = $item->getValue();
            }
            $this->setData('store_option_values_' . $storeId, $values);
        }
        return $values;
    }

    public function getLabelValues()
    {
        $values = array();
        $frontendLabel = $this->getAttributeObject()->getFrontend()->getLabel();
        if (is_array($frontendLabel)) {
            return $frontendLabel;
        }
        $values[0] = $frontendLabel;
        $storeLabels = $this->getAttributeObject()->getStoreLabels();
        foreach ($this->getStores() as $store) {
            if ($store->getId() != 0) {
                $values[$store->getId()] = isset($storeLabels[$store->getId()]) ? $storeLabels[$store->getId()] : '';
            }
        }
        return $values;
    }

    public function getOptionValues()
    {
        $attributeType = $this->getAttributeObject()->getFrontendInput();
        $defaultValues = $this->getAttributeObject()->getDefaultValue();
        if ($attributeType == 'select' || $attributeType == 'multiselect') {
            $defaultValues = explode(',', $defaultValues);
        } else {
            $defaultValues = array();
        }

        switch ($attributeType) {
            case 'select':
                $inputType = 'radio';
                break;
            case 'multiselect':
                $inputType = 'checkbox';
                break;
            default:
                $inputType = '';
                break;
        }

        $values = $this->getData('option_values');
        if (is_null($values)) {
            $values = array();
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
                ->setPositionOrder('desc', true)
                ->load();

            $helper = Mage::helper('core');
            foreach ($optionCollection as $option) {
                $value = array();
                if (in_array($option->getId(), $defaultValues)) {
                    $value['checked'] = 'checked="checked"';
                } else {
                    $value['checked'] = '';
                }

                $value['intype'] = $inputType;
                $value['id'] = $option->getId();
                $value['sort_order'] = $option->getSortOrder();
                foreach ($this->getStores() as $store) {
                    $storeValues = $this->getStoreOptionValues($store->getId());
                    $value['store' . $store->getId()] = isset($storeValues[$option->getId()])
                    ? $helper->escapeHtml($storeValues[$option->getId()]) : '';
                }
                $values[] = new Varien_Object($value);
            }
            $this->setData('option_values', $values);
        }

        return $values;
    }

    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
}
