<?php

class Ccc_Vendor_Block_Adminhtml_Vendor_Attribute_Set_Main extends Mage_Adminhtml_Block_Template
{
    
    protected function _construct()
    {
        $this->setTemplate('vendor/attribute/set/main.phtml');
    }

    protected function _prepareLayout()
    {
        $setId = $this->_getSetId();

        $this->setChild('group_tree',
            $this->getLayout()->createBlock('vendor/adminhtml_vendor_attribute_set_main_tree_group')
        );

        $this->setChild('edit_set_form',
            $this->getLayout()->createBlock('vendor/adminhtml_vendor_attribute_set_main_formset')
        );

        $this->setChild('delete_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('Delete Selected Group'),
                'onclick'   => 'editSet.submit();',
                'class'     => 'delete'
        )));

        $this->setChild('add_group_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('Add New'),
                'onclick'   => 'editSet.addGroup();',
                'class'     => 'add'
        )));

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('Back'),
                'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
                'class'     => 'back'
        )));

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('Reset'),
                'onclick'   => 'window.location.reload()'
        )));

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('Save Attribute Set'),
                'onclick'   => 'editSet.save();',
                'class'     => 'save'
        )));

        $deleteConfirmMessage = $this->jsQuoteEscape(Mage::helper('vendor')
            ->__('All products of this set will be deleted! Are you sure you want to delete this attribute set?'));
        $deleteUrl = $this->getUrl('*/*/delete', array('id' => $setId));
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('Delete Attribute Set'),
                'onclick'   => 'deleteConfirm(\'' . $deleteConfirmMessage . '\', \'' . $deleteUrl . '\')',
                'class'     => 'delete'
        )));

        $this->setChild('rename_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
                'label'     => Mage::helper('vendor')->__('New Set Name'),
                'onclick'   => 'editSet.rename()'
        )));

        return parent::_prepareLayout();
    }

    
    public function getGroupTreeHtml()
    {
        return $this->getChildHtml('group_tree');
    }

    
    public function getSetFormHtml()
    {
        return $this->getChildHtml('edit_set_form');
    }

    
    protected function _getHeader()
    {
        return Mage::helper('vendor')->__("Edit Attribute Set '%s'", $this->_getAttributeSet()->getAttributeSetName());
    }

    
    public function getMoveUrl()
    {
        return $this->getUrl('*/vendor_set/save', array('id' => $this->_getSetId()));
    }

    public function getGroupUrl()
    {
        return $this->getUrl('*/vendor_group/save', array('id' => $this->_getSetId()));
    }

    public function getGroupTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();

        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();

        foreach ($groups as $node) {
            $item = array();
            $item['text']       = $node->getAttributeGroupName();
            $item['id']         = $node->getAttributeGroupId();
            $item['cls']        = 'folder';
            $item['allowDrop']  = true;
            $item['allowDrag']  = true;

            $nodeChildren = Mage::getResourceModel('vendor/vendor_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->checkConfigurableProducts()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                $item['children'] = array();
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */
                    $attr = array(
                        'text'              => $child->getAttributeCode(),
                        'id'                => $child->getAttributeId(),
                        'cls'               => (!$child->getIsUserDefined()) ? 'system-leaf'    : 'leaf',
                        'allowDrop'         => false,
                        'allowDrag'         => true,
                        'leaf'              => true,
                        'is_user_defined'   => $child->getIsUserDefined(),
                        'is_configurable'   => false,
                        'entity_id'         => $child->getEntityAttributeId()
                    );

                    $item['children'][] = $attr;
                }
            }

            $items[] = $item;
        }

        return Mage::helper('core')->jsonEncode($items);
    }

    public function getAttributeTreeJson()
    {
        $items = array();
        $setId = $this->_getSetId();

        $collection = Mage::getResourceModel('vendor/vendor_attribute_collection')
            ->setAttributeSetFilter($setId)
            ->load();

        $attributesIds = array('0');
        
        foreach ($collection->getItems() as $item) {
            $attributesIds[] = $item->getAttributeId();
        }

        $attributes = Mage::getResourceModel('vendor/vendor_attribute_collection')
            ->setAttributesExcludeFilter($attributesIds)
            ->load();

        foreach ($attributes as $child) {
            $attr = array(
                'text'              => $child->getAttributeCode(),
                'id'                => $child->getAttributeId(),
                'cls'               => 'leaf',
                'allowDrop'         => false,
                'allowDrag'         => true,
                'leaf'              => true,
                'is_user_defined'   => $child->getIsUserDefined(),
                'is_configurable'   => false,
                'entity_id'         => $child->getEntityId()
            );

            $items[] = $attr;
        }

        if (count($items) == 0) {
            $items[] = array(
                'text'      => Mage::helper('vendor')->__('Empty'),
                'id'        => 'empty',
                'cls'       => 'folder',
                'allowDrop' => false,
                'allowDrag' => false,
            );
        }

        return Mage::helper('core')->jsonEncode($items);
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    
    public function getDeleteButtonHtml()
    {
        if ($this->getIsCurrentSetDefault()) {
            return '';
        }
        return $this->getChildHtml('delete_button');
    }

    public function getDeleteGroupButton()
    {
        return $this->getChildHtml('delete_group_button');
    }

    public function getAddGroupButton()
    {
        return $this->getChildHtml('add_group_button');
    }

    public function getRenameButton()
    {
        return $this->getChildHtml('rename_button');
    }

    protected function _getAttributeSet()
    {
        return Mage::registry('current_attribute_set');
    }

    protected function _getSetId()
    {
        return $this->_getAttributeSet()->getId();
    }

    public function getIsCurrentSetDefault()
    {
        $isDefault = $this->getData('is_current_set_default');
        if (is_null($isDefault)) {
            $defaultSetId = Mage::getModel('eav/entity_type')
                ->load(Mage::registry('entityType'))
                ->getDefaultAttributeSetId();
            $isDefault = $this->_getSetId() == $defaultSetId;
            $this->setData('is_current_set_default', $isDefault);
        }
        return $isDefault;
    }

    protected function _getSetData()
    {
        return $this->_getAttributeSet();
    }

    
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
}
